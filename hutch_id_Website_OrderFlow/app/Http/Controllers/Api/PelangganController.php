<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    public function index()
    {
        return response()->json(Pelanggan::orderBy('nama', 'asc')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'telepon' => 'required|string|min:8|max:15',
            'alamat' => 'required|string',
            'email' => 'required|email|unique:pelanggans,email',
        ], [
            'nama.required' => 'Nama pelanggan wajib diisi.',
            'telepon.required' => 'Nomor telepon wajib diisi.',
            'telepon.min' => 'Nomor telepon minimal 8 karakter.',
            'alamat.required' => 'Alamat wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
        ]);

        $pelanggan = Pelanggan::create([
            'nama' => $request->nama,
            'telepon' => $request->telepon,
            'alamat' => $request->alamat,
            'email' => $request->email,
            'jumlah_po' => 0,
        ]);

        return response()->json($pelanggan, 201);
    }

    public function update(Request $request, $id)
    {
        $pelanggan = Pelanggan::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'telepon' => 'required|string|min:8|max:15',
            'alamat' => 'required|string',
            'email' => 'required|email|unique:pelanggans,email,' . $id,
        ], [
            'nama.required' => 'Nama pelanggan wajib diisi.',
            'telepon.required' => 'Nomor telepon wajib diisi.',
            'telepon.min' => 'Nomor telepon minimal 8 karakter.',
            'alamat.required' => 'Alamat wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
        ]);

        $pelanggan->update($request->only(['nama', 'telepon', 'alamat', 'email']));

        return response()->json($pelanggan);
    }

    public function destroy($id)
    {
        $pelanggan = Pelanggan::findOrFail($id);
        $pelanggan->delete();

        return response()->json(['message' => 'Pelanggan berhasil dihapus']);
    }
}
