<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LaporanKandang;
use Illuminate\Support\Facades\Storage;

class LaporanKandangController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'kandang') {
            // Kandang hanya lihat miliknya sendiri
            $data = LaporanKandang::where('user_id', $user->id)
                ->latest()
                ->get();
        } else {
            // Admin lihat semua
            $data = LaporanKandang::with('user')->latest()->get();
        }

        return response()->json(['data' => $data]);
    }

    public function show(Request $request, $id)
    {
        $laporan = LaporanKandang::findOrFail($id);

        return response()->json(['data' => $laporan]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_mulai'     => 'required|date',
            'tanggal_selesai'   => 'required|date|after_or_equal:tanggal_mulai',
            'jumlah_ayam_awal'  => 'required|integer|min:0',
            'jumlah_ayam_mati'  => 'required|integer|min:0',
            'umur_ayam'         => 'required|integer|min:0',
            'rata_rata_bobot'   => 'required|numeric|min:0',
            'catatan'           => 'nullable|string',
            'foto'              => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $validated['user_id'] = $request->user()->id;

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('laporan_kandang', 'public');
        }

        $laporan = LaporanKandang::create($validated);

        return response()->json([
            'message' => 'Laporan kandang berhasil dibuat',
            'data'    => $laporan,
            'foto_url' => isset($validated['foto'])
                ? asset('storage/' . $validated['foto'])
                : null,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $laporan = LaporanKandang::findOrFail($id);

        // Hanya pemilik yang boleh edit
        if ($laporan->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $validated = $request->validate([
            'tanggal_mulai'     => 'sometimes|required|date',
            'tanggal_selesai'   => 'sometimes|required|date|after_or_equal:tanggal_mulai',
            'jumlah_ayam_awal'  => 'sometimes|required|integer|min:0',
            'jumlah_ayam_mati'  => 'sometimes|required|integer|min:0',
            'umur_ayam'         => 'sometimes|required|integer|min:0',
            'rata_rata_bobot'   => 'sometimes|required|numeric|min:0',
            'catatan'           => 'nullable|string',
            'foto'              => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($laporan->foto) {
                Storage::disk('public')->delete($laporan->foto);
            }
            $validated['foto'] = $request->file('foto')->store('laporan_kandang', 'public');
        }

        $laporan->update($validated);

        return response()->json([
            'message' => 'Laporan kandang berhasil diupdate',
            'data'    => $laporan,
            'foto_url' => $laporan->foto
                ? asset('storage/' . $laporan->foto)
                : null,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $laporan = LaporanKandang::findOrFail($id);

        // Hanya pemilik atau admin yang boleh hapus
        if ($laporan->user_id !== $request->user()->id && $request->user()->role !== 'admin') {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        if ($laporan->foto) {
            Storage::disk('public')->delete($laporan->foto);
        }

        $laporan->delete();

        return response()->json(['message' => 'Laporan kandang berhasil dihapus']);
    }
}