<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Stok;

class StokController extends Controller
{
    private function isGudang(Request $request): bool
    {
        return $request->user()->role === 'gudang';
    }

    public function index(Request $request)
    {
        // Semua role bisa lihat stok
        $data = Stok::with('user')->latest('tanggal_update')->get();

        return response()->json(['data' => $data]);
    }

    public function show($id)
    {
        $stok = Stok::findOrFail($id);

        return response()->json(['data' => $stok]);
    }

    public function store(Request $request)
    {
        if (!$this->isGudang($request)) {
            return response()->json(['message' => 'Akses ditolak, hanya gudang yang bisa mengelola stok'], 403);
        }

        $validated = $request->validate([
            'jenis'                 => 'required|in:whole,parting',
            'berat_per_item'        => 'required|numeric|min:0',
            'jumlah_stok'           => 'required|integer|min:0',
            'estimasi_total_berat'  => 'required|numeric|min:0',
            'tanggal_update'        => 'required|date',
            'status'                => 'required|in:aman,menipis,habis',
        ]);

        $validated['user_id'] = $request->user()->id;

        $stok = Stok::create($validated);

        return response()->json([
            'message' => 'Stok berhasil ditambahkan',
            'data'    => $stok,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        if (!$this->isGudang($request)) {
            return response()->json(['message' => 'Akses ditolak, hanya gudang yang bisa mengelola stok'], 403);
        }

        $stok = Stok::findOrFail($id);

        $validated = $request->validate([
            'jenis'                 => 'sometimes|required|in:whole,parting',
            'berat_per_item'        => 'sometimes|required|numeric|min:0',
            'jumlah_stok'           => 'sometimes|required|integer|min:0',
            'estimasi_total_berat'  => 'sometimes|required|numeric|min:0',
            'tanggal_update'        => 'sometimes|required|date',
            'status'                => 'sometimes|required|in:aman,menipis,habis',
        ]);

        // Auto-update tanggal_update ke hari ini jika tidak diisi
        if (!isset($validated['tanggal_update'])) {
            $validated['tanggal_update'] = now()->toDateString();
        }

        $stok->update($validated);

        return response()->json([
            'message' => 'Stok berhasil diupdate',
            'data'    => $stok,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        if (!$this->isGudang($request) && $request->user()->role !== 'admin') {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $stok = Stok::findOrFail($id);
        $stok->delete();

        return response()->json(['message' => 'Stok berhasil dihapus']);
    }
}