<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Http\Request;

class PesananController extends Controller
{
    public function index()
    {
        return response()->json(Pesanan::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pemesan' => 'required|string|max:255',
            'no_hp' => 'required|string|max:20',
            'bobot' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
            'kuantitas' => 'required|integer|min:1',
            'total' => 'required|numeric|min:0',
            'alamat_pengiriman' => 'required|string',
        ]);

        $pesanan = Pesanan::create($validated);

        return response()->json([
            'message' => 'Data berhasil ditambah',
            'data' => $pesanan
        ], 201);
    }

    public function show($id)
    {
        return response()->json(
            Pesanan::findOrFail($id)
        );
    }

    public function update(Request $request, $id)
    {
        $pesanan = Pesanan::findOrFail($id);

        $validated = $request->validate([
            'nama_pemesan' => 'required|string|max:255',
            'no_hp' => 'required|string|max:20',
            'bobot' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
            'kuantitas' => 'required|integer|min:1',
            'total' => 'required|numeric|min:0',
            'alamat_pengiriman' => 'required|string',
        ]);

        $pesanan->update($validated);

        return response()->json([
            'message' => 'Data berhasil diupdate',
            'data' => $pesanan
        ]);
    }

    public function destroy($id)
    {
        Pesanan::destroy($id);

        return response()->json([
            'message' => 'Data berhasil dihapus'
        ]);
    }
}