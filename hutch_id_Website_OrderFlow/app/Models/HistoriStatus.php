<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class HistoriStatus extends Model
{
    use HasFactory;

    protected $table = 'histori_status';
    protected $fillable = ['pesanan_id', 'user_id', 'status', 'keterangan'];
    public $timestamps = false;
    protected $casts = [
        'created_at' => 'datetime',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
