<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:administrator']);
    }

    public function index()
    {
        $users = User::orderBy('id', 'desc')->get();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = [
            'administrator' => 'Administrator',
            'staf_penjualan' => 'Staf Penjualan',
            'operator_gudang' => 'Operator Gudang',
            'pemilik_umkm' => 'Pemilik UMKM',
        ];

        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->only(['email', 'role', 'password']);

        $validator = Validator::make($data, [
            'email' => 'required|email|unique:users,email',
            'role' => 'required|string|in:administrator,staf_penjualan,operator_gudang,pemilik_umkm',
            'password' => 'required|string|min:6',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = new User();
        $user->email = $data['email'];
        $user->role = $data['role'];
        $user->password = Hash::make($data['password']);
        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $roles = [
            'administrator' => 'Administrator',
            'staf_penjualan' => 'Staf Penjualan',
            'operator_gudang' => 'Operator Gudang',
            'pemilik_umkm' => 'Pemilik UMKM',
        ];

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->only(['email', 'role', 'password']);

        $rules = [
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|string|in:administrator,staf_penjualan,operator_gudang,pemilik_umkm',
        ];

        if (!empty($data['password'])) {
            $rules['password'] = 'string|min:6';
        }

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user->email = $data['email'];
        $user->role = $data['role'];

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        // Prevent deleting own account
        if (auth()->id() === $user->id) {
            return redirect()->route('admin.users.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}
