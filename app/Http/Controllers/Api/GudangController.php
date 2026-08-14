<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Gudang;
use App\Models\Stok;
use Illuminate\Http\Request;

class GudangController extends Controller
{
    public function index()
    {
        return response()->json(['data' => Gudang::orderBy('nama')->get()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255|unique:gudangs,nama',
        ]);

        $gudang = Gudang::create($validated);

        return response()->json([
            'message' => 'Gudang berhasil ditambahkan',
            'data' => $gudang,
        ], 201);
    }

    public function destroy(Gudang $gudang)
    {
        $dipakai = Stok::where('gudang', $gudang->nama)->exists();

        if ($dipakai) {
            return response()->json([
                'message' => 'Gudang ini masih dipakai di data stok, tidak bisa dihapus',
            ], 422);
        }

        $gudang->delete();

        return response()->json([
            'message' => 'Gudang berhasil dihapus',
        ]);
    }
}
