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
        'total_nilai', 'status', 'catatan', 'created_by', 'tanggal_dikirim', 'nomor_resi', 'alasan_pembatalan'
    ];

    protected $casts = [
        'tanggal_pesanan' => 'date',
        'tanggal_pengiriman' => 'date',
        'tanggal_dikirim' => 'date',
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

    /**
     * Display name for "Disimpan oleh" on the PO detail page.
     *
     * Always reflects whoever actually created the PO: shows their name if
     * set, otherwise falls back to their role label (e.g. "Staf Penjualan"
     * for staff, "Admin" for administrators). Only falls back to "Sistem"
     * when there is genuinely no creator on record.
     */
    public function getCreatorLabelAttribute(): string
    {
        return $this->creator?->display_name ?? 'Sistem';
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
