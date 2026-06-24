<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;

    protected $table = 'pelanggan';
    protected $fillable = ['nama', 'alamat', 'telepon', 'email', 'nomor_whatsapp', 'catatan'];

    public function pesanan()
    {
        return $this->hasMany(Pesanan::class);
    }
}
