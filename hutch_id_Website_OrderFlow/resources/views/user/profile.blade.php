@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg border-0" style="border-radius: 20px; background: linear-gradient(135deg, rgba(37, 117, 215, 0.05), rgba(0, 212, 255, 0.05));">
                <div class="card-header bg-primary text-white" style="border-radius: 20px 20px 0 0;">
                    <h4 class="mb-0"><i class="fas fa-user-circle me-2"></i>Profile Pengguna</h4>
                </div>
                
                <div class="card-body p-4">
                    <div class="profile-info mb-4">
                        <div class="info-group mb-3">
                            <label class="fw-bold text-muted" style="font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">Email</label>
                            <div class="info-value" style="font-size: 1.1rem; color: #2575d7;">
                                {{ $user->email }}
                            </div>
                        </div>

                        <div class="info-group mb-3">
                            <label class="fw-bold text-muted" style="font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">Role</label>
                            <div class="info-value" style="font-size: 1.1rem; color: #2575d7;">
                                {{ $roleLabel }}
                            </div>
                        </div>

                        <div class="info-group mb-3">
                            <label class="fw-bold text-muted" style="font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">Pengguna Login</label>
                            <div class="info-value" style="font-size: 1.1rem; color: #2575d7;">
                                {{ $roleLabel }} {{ $user->account_number ?? 1 }}
                            </div>
                        </div>

                        <div class="info-group mb-3">
                            <label class="fw-bold text-muted" style="font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                <i class="fab fa-whatsapp" style="color: #25D366; margin-right: 0.5rem;"></i>Nomor WhatsApp Business (Hutch.id)
                            </label>
                            <div class="info-value" style="font-size: 1.1rem; color: #25D366; font-weight: 600;">
                                {{ $whatsappBusinessNumberFormatted }}
                            </div>
                            <small class="text-muted d-block mt-2">📱 Nomor resmi Hutch.id untuk komunikasi pesanan</small>
                        </div>
                    </div>

                    <hr style="border-top: 2px solid rgba(37, 117, 215, 0.1);">

                    <div class="mt-4">
                        <button class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#deleteConfirmModal" style="border-radius: 10px; font-weight: 600;">
                            <i class="fas fa-trash-alt me-2"></i>Hapus Akun
                        </button>
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary w-100" style="border-radius: 10px; font-weight: 600;">
                            <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
            <div class="modal-header border-0 bg-danger text-white" style="border-radius: 15px 15px 0 0;">
                <h5 class="modal-title" id="deleteConfirmLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Penghapusan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="mb-0" style="font-size: 1rem; color: #333;">
                    Apakah Anda yakin ingin menghapus akun ini? Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>
            <div class="modal-footer border-0 bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">
                    <i class="fas fa-times me-2"></i>Batal
                </button>
                <form method="POST" action="{{ route('user.destroy') }}" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" style="border-radius: 8px;">
                        <i class="fas fa-check me-2"></i>Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .info-group {
        padding: 1rem;
        background: rgba(255, 255, 255, 0.5);
        border-radius: 10px;
        border-left: 4px solid #2575d7;
    }

    .info-value {
        margin-top: 0.5rem;
        word-break: break-all;
    }
</style>
@endsection
