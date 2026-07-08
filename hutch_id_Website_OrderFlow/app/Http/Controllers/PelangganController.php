<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // If API request, return JSON list
        if ($request->expectsJson() || $request->is('api/*')) {
            $pelanggan = Pelanggan::withCount('pesanan')
                ->when($request->cari, function ($query, $cari) {
                    $query->where('nama', 'like', '%' . $cari . '%');
                })
                ->latest()
                ->get();
            
            return response()->json($pelanggan);
        }

        // Web request, return paginated view
        $pelanggan = Pelanggan::withCount('pesanan')
            ->when($request->cari, function ($query, $cari) {
                $query->where('nama', 'like', '%' . $cari . '%');
            })
            ->latest()
            ->paginate(12);

        return view('pelanggan.index', compact('pelanggan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pelanggan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string|max:500',
            'telepon' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
        ]);

        $pelanggan = Pelanggan::create($validated);

        if ($request->expectsJson()) {
            return response()->json($pelanggan, 201);
        }

        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Pelanggan $pelanggan)
    {
        return response()->json($pelanggan);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pelanggan $pelanggan)
    {
        return view('pelanggan.edit', compact('pelanggan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pelanggan $pelanggan)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string|max:500',
            'telepon' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
        ]);

        $pelanggan->update($validated);

        if ($request->expectsJson()) {
            return response()->json($pelanggan, 200);
        }

        return redirect()->route('pelanggan.index')->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pelanggan $pelanggan, Request $request)
    {
        // pemilik_umkm, administrator, dan staf_penjualan dapat menghapus pelanggan
        if (!in_array(auth()->user()->role, ['pemilik_umkm', 'administrator', 'staf_penjualan'])) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            abort(403, 'Anda tidak memiliki izin untuk menghapus pelanggan ini.');
        }

        $pelanggan->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Pelanggan berhasil dihapus'], 200);
        }

        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil dihapus.');
    }

    public function search(Request $request)
    {
        $query = $request->query('q');

        $pelanggan = Pelanggan::when($query, function ($builder, $value) {
            $builder->where('nama', 'like', '%' . $value . '%');
        })
        ->limit(10)
        ->get(['id', 'nama', 'alamat', 'telepon', 'email']);

        return response()->json($pelanggan);
    }
}
