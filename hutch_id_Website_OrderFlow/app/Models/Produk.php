<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk';
    protected $fillable = ['nama', 'foto', 'harga_jual', 'stok', 'keterangan', 'created_by'];
    protected $appends = ['foto_url'];

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
        
        // Return absolute URL untuk backend
        $basePath = rtrim(config('app.url'), '/');
        
        // Jika path dimulai dengan 'images/', tambahkan base URL
        if (strpos($foto, 'images/') === 0) {
            return $basePath . '/' . $foto;
        }
        
        // Extract filename dari path
        $filename = basename($foto);
        
        // Return dari /images/ folder dengan full URL
        return $basePath . '/images/' . $filename;
    }
}

