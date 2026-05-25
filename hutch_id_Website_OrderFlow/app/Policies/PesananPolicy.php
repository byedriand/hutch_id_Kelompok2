<?php

namespace App\Policies;

use App\Models\Pesanan;
use App\Models\User;

class PesananPolicy
{
    /**
     * Determine if the user can view the PO
     */
    public function view(User $user, Pesanan $pesanan): bool
    {
        // Administrator can view all
        if ($user->role === 'administrator') {
            return true;
        }

        // Pemilik UMKM can view all
        if ($user->role === 'pemilik_umkm') {
            return true;
        }

        // Operator Gudang can view confirmed POs only
        if ($user->role === 'operator_gudang') {
            return in_array($pesanan->status, ['dikonfirmasi', 'dalam_produksi', 'siap_kirim', 'selesai']);
        }

        // Staf Penjualan can only view their own POs
        if ($user->role === 'staf_penjualan') {
            return $pesanan->created_by === $user->id;
        }

        return false;
    }

    /**
     * Determine if the user can create a PO
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['staf_penjualan', 'pemilik_umkm', 'administrator']);
    }

    /**
     * Determine if the user can update a PO
     */
    public function update(User $user, Pesanan $pesanan): bool
    {
        return in_array($user->role, ['pemilik_umkm', 'administrator']);
    }

    /**
     * Determine if the user can confirm a PO
     */
    public function confirm(User $user, Pesanan $pesanan): bool
    {
        return in_array($user->role, ['pemilik_umkm', 'administrator']);
    }

    /**
     * Determine if the user can change PO status
     */
    public function changeStatus(User $user, Pesanan $pesanan): bool
    {
        if ($user->role === 'administrator') {
            return true;
        }

        if ($user->role === 'pemilik_umkm') {
            return true;
        }

        if ($user->role === 'operator_gudang') {
            // Can only change status for confirmed POs
            return in_array($pesanan->status, ['dikonfirmasi', 'dalam_produksi', 'siap_kirim']);
        }

        if ($user->role === 'staf_penjualan') {
            // Staf Penjualan dapat membatalkan PO yang mereka buat sebelum dikonfirmasi
            return $pesanan->created_by === $user->id && $pesanan->status === 'menunggu_konfirmasi';
        }

        return false;
    }

    /**
     * Determine if the user can cancel a PO
     */
    public function cancel(User $user, Pesanan $pesanan): bool
    {
        return in_array($user->role, ['pemilik_umkm', 'administrator']);
    }

    /**
     * Determine if the user can delete a PO
     */
    public function delete(User $user, Pesanan $pesanan): bool
    {
        return in_array($user->role, ['pemilik_umkm', 'administrator']);
    }

    /**
     * Determine if the user can download PDF
     */
    public function downloadPdf(User $user, Pesanan $pesanan): bool
    {
        return in_array($user->role, ['staf_penjualan', 'pemilik_umkm', 'operator_gudang', 'administrator']);
    }

    /**
     * Check if user can perform specific status change
     */
    public function canChangeStatusTo(User $user, Pesanan $pesanan, string $newStatus): bool
    {
        if ($user->role === 'administrator') {
            return true;
        }

        if ($user->role === 'pemilik_umkm') {
            // Can change to: dalam_produksi, siap_kirim, selesai
            return in_array($newStatus, ['dalam_produksi', 'siap_kirim', 'selesai']);
        }

        if ($user->role === 'operator_gudang') {
            // Can only change to: dalam_produksi
            return $newStatus === 'dalam_produksi';
        }

        return false;
    }
}
