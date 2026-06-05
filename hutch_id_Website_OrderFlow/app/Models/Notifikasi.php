<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Notifikasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'pesanan_id',
        'tipe',
        'judul',
        'pesan',
        'data',
        'untuk_roles',
        'created_by',
        'dibaca_at',
    ];

    protected $casts = [
        'data' => 'array',
        'untuk_roles' => 'array',
        'dibaca_at' => 'datetime',
    ];

    // Accessor untuk convert created_at ke Jakarta timezone
    public function getCreatedAtAttribute($value)
    {
        if ($value) {
            return Carbon::parse($value, 'UTC')->setTimezone('Asia/Jakarta');
        }
        return $value;
    }

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeNotBaca($query)
    {
        return $query->whereNull('dibaca_at');
    }
}
