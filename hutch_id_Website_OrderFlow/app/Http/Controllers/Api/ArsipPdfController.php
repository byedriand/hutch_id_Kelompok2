<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ArsipPdf;
use Illuminate\Http\Request;

class ArsipPdfController extends Controller
{
    public function index()
    {
        return response()->json(ArsipPdf::orderBy('id', 'desc')->get());
    }

    public function destroy($id)
    {
        $pdf = ArsipPdf::findOrFail($id);
        $pdf->delete();

        return response()->json(['message' => 'Arsip PDF berhasil dihapus']);
    }
}
