<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoriStatus extends Model
{
    use HasFactory;

    protected $table = 'histori_status';
    protected $fillable = ['pesanan_id', 'user_id', 'status', 'keterangan'];
    public $timestamps = false;

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
