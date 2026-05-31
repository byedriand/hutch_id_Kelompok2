<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk';
    protected $fillable = ['nama', 'foto', 'harga_jual', 'stok', 'keterangan'];

    public function detailPesanan()
    {
        return $this->hasMany(DetailPesanan::class);
    }

    /**
     * Get the full URL untuk foto produk
     * Handles both 'images/' dan 'storage/' paths
     */
    public function getFotoUrlAttribute()
    {
        $foto = $this->foto;
        
        if (!$foto) {
            return null;
        }
        
        // Jika sudah URL lengkap, return as-is
        if (filter_var($foto, FILTER_VALIDATE_URL)) {
            return $foto;
        }
        
        // Jika path dimulai dengan 'images/', gunakan asset() langsung
        if (strpos($foto, 'images/') === 0) {
            return asset($foto);
        }
        
        // Jika path dari storage folder, tambahkan 'storage/'
        return asset('storage/' . ltrim($foto, '/'));
    }
}

