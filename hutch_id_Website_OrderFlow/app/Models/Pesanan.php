<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    use HasFactory;

    protected $table = 'pesanan';
    protected $fillable = [
        'nomor_po', 'tanggal_pesanan', 'tanggal_pengiriman', 'pelanggan_id',
        'total_nilai', 'status', 'catatan', 'created_by'
    ];

    protected $casts = [
        'tanggal_pesanan' => 'date',
        'tanggal_pengiriman' => 'date',
    ];

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function detailPesanan()
    {
        return $this->hasMany(DetailPesanan::class);
    }

    public function historiStatus()
    {
        return $this->hasMany(HistoriStatus::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getStokCukupAttribute()
    {
        foreach ($this->detailPesanan as $detail) {
            if (! $detail->produk) {
                return false;
            }

            if ($detail->jumlah > $detail->produk->stok) {
                return false;
            }
        }

        return true;
    }
}
