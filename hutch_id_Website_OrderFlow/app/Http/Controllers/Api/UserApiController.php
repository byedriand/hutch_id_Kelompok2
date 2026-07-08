<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * API Controller untuk Manajemen Pengguna (Mobile).
 * Hanya bisa diakses oleh role 'administrator'.
 *
 * Tambahkan routes berikut ke routes/api.php di dalam
 * middleware group 'auth:sanctum':
 *
 *   Route::middleware('auth:sanctum')->group(function () {
 *       // ... existing routes ...
 *
 *       // User Management API (Administrator only)
 *       Route::get('/users', [UserApiController::class, 'index']);
 *       Route::post('/users', [UserApiController::class, 'store']);
 *       Route::put('/users/{user}', [UserApiController::class, 'update']);
 *       Route::delete('/users/{user}', [UserApiController::class, 'destroy']);
 *   });
 */
class UserApiController extends Controller
{
    /**
     * Validasi bahwa user yang login adalah administrator.
     */
    private function checkAdmin(Request $request)
    {
        $user = $request->user();
        if (!$user || $user->role !== 'administrator') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya Administrator yang dapat mengakses fitur ini.',
            ], 403);
        }
        return null;
    }

    /**
     * GET /api/users
     * Ambil semua pengguna (hanya Administrator).
     */
    public function index(Request $request)
    {
        $denied = $this->checkAdmin($request);
        if ($denied) return $denied;

        $users = User::orderBy('id', 'desc')->get()->map(function ($user) {
            return [
                'id'         => $user->id,
                'email'      => $user->email,
                'role'       => $user->role,
                'created_at' => $user->created_at?->toIso8601String(),
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $users,
        ]);
    }

    /**
     * POST /api/users
     * Tambah pengguna baru (hanya Administrator).
     * Body: { email, role, password }
     */
    public function store(Request $request)
    {
        $denied = $this->checkAdmin($request);
        if ($denied) return $denied;

        $validator = Validator::make($request->all(), [
            'email'    => 'required|email|unique:users,email',
            'role'     => 'required|string|in:administrator,staf_penjualan,operator_gudang,pemilik_umkm',
            'password' => 'required|string|min:6',
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'email.unique'      => 'Email sudah terdaftar.',
            'role.required'     => 'Role wajib dipilih.',
            'role.in'           => 'Role tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal 6 karakter.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user = User::create([
            'email'    => $request->email,
            'role'     => $request->role,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengguna berhasil ditambahkan.',
            'data'    => [
                'id'         => $user->id,
                'email'      => $user->email,
                'role'       => $user->role,
                'created_at' => $user->created_at?->toIso8601String(),
            ],
        ], 201);
    }

    /**
     * PUT /api/users/{user}
     * Edit pengguna (hanya Administrator).
     * Body: { email, role, password? }
     */
    public function update(Request $request, User $user)
    {
        $denied = $this->checkAdmin($request);
        if ($denied) return $denied;

        $rules = [
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role'  => 'required|string|in:administrator,staf_penjualan,operator_gudang,pemilik_umkm',
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'string|min:6';
        }

        $validator = Validator::make($request->all(), $rules, [
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
            'email.unique'   => 'Email sudah digunakan akun lain.',
            'role.required'  => 'Role wajib dipilih.',
            'role.in'        => 'Role tidak valid.',
            'password.min'   => 'Password minimal 6 karakter.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user->email = $request->email;
        $user->role  = $request->role;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Pengguna berhasil diperbarui.',
            'data'    => [
                'id'         => $user->id,
                'email'      => $user->email,
                'role'       => $user->role,
                'created_at' => $user->created_at?->toIso8601String(),
            ],
        ]);
    }

    /**
     * DELETE /api/users/{user}
     * Hapus pengguna (hanya Administrator).
     * Tidak bisa hapus akun sendiri.
     */
    public function destroy(Request $request, User $user)
    {
        $denied = $this->checkAdmin($request);
        if ($denied) return $denied;

        if ($request->user()->id === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat menghapus akun Anda sendiri.',
            ], 403);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pengguna berhasil dihapus.',
        ]);
    }
}
