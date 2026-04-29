<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LaporanGudang;
use Illuminate\Support\Facades\Storage;

class LaporanGudangController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'gudang') {
            $data = LaporanGudang::where('user_id', $user->id)->latest()->get();
        } else {
            $data = LaporanGudang::with('user')->latest()->get();
        }

        return response()->json(['data' => $data]);
    }

    public function show($id)
    {
        $laporan = LaporanGudang::findOrFail($id);

        return response()->json([
            'data'     => $laporan,
            'foto_url' => $laporan->foto ? asset('storage/' . $laporan->foto) : null,
        ]);
    }

    public function store(Request $request)
    {
        // Hanya role gudang yang boleh buat laporan gudang
        if ($request->user()->role !== 'gudang') {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $validated = $request->validate([
            'tanggal_mulai'      => 'required|date',
            'tanggal_selesai'    => 'required|date|after_or_equal:tanggal_mulai',
            'stok_awal'          => 'required|integer|min:0',
            'stok_masuk'         => 'required|integer|min:0',
            'jumlah_daging_jual' => 'required|integer|min:0',
            'stok_akhir'         => 'nullable|integer|min:0',
            'catatan'            => 'nullable|string',
            'foto'               => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $validated['user_id'] = $request->user()->id;

        // Auto-hitung stok_akhir jika tidak diisi
        if (empty($validated['stok_akhir'])) {
            $validated['stok_akhir'] = $validated['stok_awal']
                + $validated['stok_masuk']
                - $validated['jumlah_daging_jual'];
        }

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('laporan_gudang', 'public');
        }

        $laporan = LaporanGudang::create($validated);

        return response()->json([
            'message'  => 'Laporan gudang berhasil dibuat',
            'data'     => $laporan,
            'foto_url' => $laporan->foto ? asset('storage/' . $laporan->foto) : null,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $laporan = LaporanGudang::findOrFail($id);

        if ($laporan->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $validated = $request->validate([
            'tanggal_mulai'      => 'sometimes|required|date',
            'tanggal_selesai'    => 'sometimes|required|date|after_or_equal:tanggal_mulai',
            'stok_awal'          => 'sometimes|required|integer|min:0',
            'stok_masuk'         => 'sometimes|required|integer|min:0',
            'jumlah_daging_jual' => 'sometimes|required|integer|min:0',
            'stok_akhir'         => 'nullable|integer|min:0',
            'catatan'            => 'nullable|string',
            'foto'               => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Recalculate stok_akhir jika field terkait berubah
        $stokAwal  = $validated['stok_awal']          ?? $laporan->stok_awal;
        $stokMasuk = $validated['stok_masuk']         ?? $laporan->stok_masuk;
        $terjual   = $validated['jumlah_daging_jual'] ?? $laporan->jumlah_daging_jual;

        if (!isset($validated['stok_akhir'])) {
            $validated['stok_akhir'] = $stokAwal + $stokMasuk - $terjual;
        }

        if ($request->hasFile('foto')) {
            if ($laporan->foto) {
                Storage::disk('public')->delete($laporan->foto);
            }
            $validated['foto'] = $request->file('foto')->store('laporan_gudang', 'public');
        }

        $laporan->update($validated);

        return response()->json([
            'message'  => 'Laporan gudang berhasil diupdate',
            'data'     => $laporan,
            'foto_url' => $laporan->foto ? asset('storage/' . $laporan->foto) : null,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $laporan = LaporanGudang::findOrFail($id);

        if ($laporan->user_id !== $request->user()->id && $request->user()->role !== 'admin') {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        if ($laporan->foto) {
            Storage::disk('public')->delete($laporan->foto);
        }

        $laporan->delete();

        return response()->json(['message' => 'Laporan gudang berhasil dihapus']);
    }
}