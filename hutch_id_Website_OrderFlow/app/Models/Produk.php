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
     * Returns image from /images/ folder (compatible with Windows)
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
        
        // Jika path dimulai dengan 'images/', sudah benar
        if (strpos($foto, 'images/') === 0) {
            return '/' . $foto;
        }
        
        // Extract filename dari path
        $filename = basename($foto);
        
        // Return dari /images/ folder (semua file sudah di-copy ke sini)
        return '/images/' . $filename;
    }
}

