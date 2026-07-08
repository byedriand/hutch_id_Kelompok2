@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
        <div>
            <h3 class="mb-0">Manajemen Pengguna</h3>
            <p class="text-muted small mb-0">Kelola akun dan peran pengguna. Admin dapat menambah, mengedit, atau menghapus akun.</p>
        </div>
        <button type="button" class="btn btn-primary btn-add-user" style="background: linear-gradient(135deg,#2d7dd2,#1e5aa8); border:none;">
            <i class="fas fa-user-plus me-2"></i>Tambah Pengguna
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm rounded-3">
        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:64px">No</th>
                            <th>Email</th>
                            <th style="width:160px">Role</th>
                            <th class="d-none d-md-table-cell" style="width:180px">Dibuat</th>
                            <th style="width:160px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $u)
                            <tr>
                                <td class="fw-bold">{{ $loop->iteration }}</td>
                                <td class="text-truncate" style="max-width:360px">{{ $u->email }}</td>
                                <td>
                                    @php
                                        $labels = [
                                            'administrator' => 'Administrator',
                                            'staf_penjualan' => 'Staf Penjualan',
                                            'operator_gudang' => 'Operator Gudang',
                                            'pemilik_umkm' => 'Pemilik UMKM',
                                        ];
                                        $label = $labels[$u->role] ?? $u->role;
                                    @endphp
                                    <span class="badge bg-primary" style="background: linear-gradient(135deg,#2d7dd2,#1e5aa8); font-weight:700;">{{ $label }}</span>
                                </td>
                                <td class="d-none d-md-table-cell">{{ optional($u->created_at)->format('d M Y H:i') }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary me-1 btn-edit-user"
                                        data-id="{{ $u->id }}"
                                        data-email="{{ $u->email }}"
                                        data-role="{{ $u->role }}">
                                        Edit
                                    </button>
                                    @if(auth()->id() !== $u->id)
                                        <button type="button" class="btn btn-sm btn-danger btn-delete-user" data-action="{{ route('admin.users.destroy', $u->id) }}">Hapus</button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Tweak table for admin users to match global theme and be responsive */
    .table-responsive { --bs-table-bg: transparent; }
    .card .table thead th { border-bottom: 0; }
    .card { background: linear-gradient(180deg,#ffffff,#f8fbff); }
    @media (max-width: 576px) {
        .sidebar { display: none; }
        .card-body { padding: 0.75rem; }
        .text-truncate { max-width: 180px !important; }
    }
</style>
@endpush

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
        const addUserButton = document.querySelector('.btn-add-user');
        const editButtons = document.querySelectorAll('.btn-edit-user');
        const modalHtml = `
                <div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header" style="background: linear-gradient(90deg,#2d7dd2,#1e5aa8); color: #fff;">
                                <h5 class="modal-title" id="userModalTitle">Tambah Pengguna</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form id="userForm" method="POST">
                                @csrf
                                <input type="hidden" name="_method" id="userFormMethod" value="POST">
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" id="userEmail" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Role</label>
                                        <select name="role" id="userRole" class="form-select" required>
                                            <option value="administrator">Administrator</option>
                                            <option value="staf_penjualan">Staf Penjualan</option>
                                            <option value="operator_gudang">Operator Gudang</option>
                                            <option value="pemilik_umkm">Pemilik UMKM</option>
                                        </select>
                                    </div>
                                    <div class="mb-3" id="currentPasswordDiv" style="display: none;">
                                        <label class="form-label">Password saat ini</label>
                                        <div style="position: relative;">
                                            <div class="form-control" style="background-color: #f0f0f0; border: 1px solid #ddd; padding: 10px 12px; border-radius: 4px; display: flex; align-items: center;">
                                                <i class="fas fa-lock" style="color: #999; margin-right: 8px;"></i>
                                                <span style="color: #999; letter-spacing: 2px;">••••••••</span>
                                            </div>
                                        </div>
                                        <small style="color: #666; display: block; margin-top: 5px;">Password saat ini ditampilkan sebagai placeholder; password asli tidak dapat ditampilkan karena disimpan sebagai hash.</small>
                                    </div>
                                    <div class="mb-3" id="newPasswordDiv">
                                        <label class="form-label" id="userPasswordLabel">Password</label>
                                        <div style="position: relative;">
                                            <input type="password" name="password" id="userPassword" class="form-control" style="padding-right: 40px;">
                                            <button type="button" class="btn" id="toggleNewPassword" style="position: absolute; right: 0; top: 0; border: none; background: none; color: #666; padding: 8px 12px; z-index: 10;">
                                                <i class="fas fa-eye-slash"></i>
                                            </button>
                                        </div>
                                        <div class="form-text" id="userPasswordHint">Kosongkan jika tidak ingin mengubah password.</div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary" style="background: linear-gradient(90deg,#2d7dd2,#1e5aa8); border: none;" id="userSubmitBtn">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
        `;

        if (!document.getElementById('userModal')) {
                document.body.insertAdjacentHTML('beforeend', modalHtml);
        }

        const userModalEl = document.getElementById('userModal');
        const userModal = new bootstrap.Modal(userModalEl);
        const userForm = document.getElementById('userForm');
        const userModalTitle = document.getElementById('userModalTitle');
        const userPasswordLabel = document.getElementById('userPasswordLabel');
        const userPasswordHint = document.getElementById('userPasswordHint');
        const userPassword = document.getElementById('userPassword');
        const userSubmitBtn = document.getElementById('userSubmitBtn');
        const userFormMethod = document.getElementById('userFormMethod');
        const currentPasswordDiv = document.getElementById('currentPasswordDiv');
        const currentPasswordDisplay = document.getElementById('currentPasswordDisplay');
        const newPasswordDiv = document.getElementById('newPasswordDiv');
        const toggleNewPassword = document.getElementById('toggleNewPassword');
        let showNewPassword = false;

        if (addUserButton) {
            addUserButton.addEventListener('click', function () {
                userModalTitle.textContent = 'Tambah Pengguna';
                userPasswordLabel.textContent = 'Password';
                userPasswordHint.textContent = '';
                userPassword.required = true;
                userPassword.readOnly = false;
                userPassword.value = '';
                userSubmitBtn.textContent = 'Simpan';
                userFormMethod.value = 'POST';
                userForm.action = '{{ route("admin.users.store") }}';
                document.getElementById('userEmail').value = '';
                document.getElementById('userRole').value = 'staf_penjualan';
                currentPasswordDiv.style.display = 'none';
                newPasswordDiv.style.display = 'block';
                userModal.show();
            });
        }

        editButtons.forEach(btn => {
                btn.addEventListener('click', function () {
                    const id = this.dataset.id;
                    const email = this.dataset.email;
                    const role = this.dataset.role;

                    userModalTitle.textContent = 'Edit Pengguna';
                    userPasswordLabel.textContent = 'Password baru';
                    userPasswordHint.textContent = 'Kosongkan jika tidak ingin mengubah password.';
                    userPassword.required = false;
                    userPassword.readOnly = false;
                    userPassword.value = '';
                    userSubmitBtn.textContent = 'Simpan Perubahan';
                    userFormMethod.value = 'PUT';
                    userForm.action = '{{ url("admin/users") }}' + '/' + id;
                    document.getElementById('userEmail').value = email;
                    document.getElementById('userRole').value = role;
                    currentPasswordDiv.style.display = 'block';
                    newPasswordDiv.style.display = 'block';

                    userModal.show();
                });
        });

        // Delete confirmation modal
        const deleteButtons = document.querySelectorAll('.btn-delete-user');
        const deleteModalHtml = `
            <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Konfirmasi Hapus</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="deleteUserForm" method="POST">
                            @csrf
                            @method('DELETE')
                            <div class="modal-body">
                                <p>Anda yakin ingin menghapus pengguna ini? Aksi ini tidak dapat dibatalkan.</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-danger">Hapus</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        `;

        if (!document.getElementById('deleteConfirmModal')) {
                document.body.insertAdjacentHTML('beforeend', deleteModalHtml);
        }

        // Toggle new password visibility
        if (toggleNewPassword) {
            toggleNewPassword.addEventListener('click', function(e) {
                e.preventDefault();
                showNewPassword = !showNewPassword;
                if (showNewPassword) {
                    userPassword.type = 'text';
                    toggleNewPassword.innerHTML = '<i class="fas fa-eye"></i>';
                } else {
                    userPassword.type = 'password';
                    toggleNewPassword.innerHTML = '<i class="fas fa-eye-slash"></i>';
                }
            });
        }

        const deleteModalEl = document.getElementById('deleteConfirmModal');
        const deleteModal = new bootstrap.Modal(deleteModalEl);
        const deleteForm = document.getElementById('deleteUserForm');

        deleteButtons.forEach(btn => {
                btn.addEventListener('click', function () {
                        const action = this.dataset.action;
                        deleteForm.action = action;
                        deleteModal.show();
                });
        });
});
</script>
@endpush
