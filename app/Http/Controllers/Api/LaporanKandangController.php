<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LaporanKandang;
use App\Models\SiklusKandang;
use Illuminate\Support\Facades\Storage;

class LaporanKandangController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if ($user->role === 'kandang') {
            $data = LaporanKandang::where('user_id', $user->id)->latest()->get();
        } else {
            $data = LaporanKandang::with('user')->latest()->get();
        }
        return response()->json(['data' => $data]);
    }

    public function show($id)
    {
        $laporan = LaporanKandang::findOrFail($id);
        return response()->json([
            'data'     => $laporan,
            'foto_url' => $laporan->foto ? asset('storage/' . $laporan->foto) : null,
        ]);
    }

    public function store(Request $request)
    {
    if ($request->user()->role !== 'kandang') {
        return response()->json(['message' => 'Akses ditolak'], 403);
    }

    $siklus = SiklusKandang::where('user_id', $request->user()->id)
        ->where('status', 'berjalan')
        ->latest('tanggal_mulai')
        ->first();

    if (!$siklus) {
        return response()->json([
            'message' => 'Belum ada siklus kandang yang aktif. Mulai siklus baru dulu sebelum lapor.',
        ], 422);
    }

    // Cek apakah ini laporan pertama di siklus ini
    $laporanTerakhir = LaporanKandang::where('siklus_id', $siklus->id)
        ->latest('tanggal')
        ->first();

    $rules = [
        'tanggal'          => 'required|date',
        'jumlah_ayam_mati' => 'required|integer|min:0',
        'rata_rata_bobot'  => 'required|numeric|min:0',
        'catatan'          => 'nullable|string',
        'foto'             => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ];

    // Hanya laporan pertama yang wajib isi jumlah_ayam_awal manual
    if (!$laporanTerakhir) {
        $rules['jumlah_ayam_awal'] = 'required|integer|min:0';
    }

    $validated = $request->validate($rules);

    if ($laporanTerakhir) {
        // Ambil sisa ayam dari laporan sebelumnya
        $validated['jumlah_ayam_awal'] = $laporanTerakhir->jumlah_ayam_awal - $laporanTerakhir->jumlah_ayam_mati;
    }

    // Validasi tambahan: ayam mati nggak boleh lebih dari stok yang ada
    if ($validated['jumlah_ayam_mati'] > $validated['jumlah_ayam_awal']) {
        return response()->json([
            'message' => 'Jumlah ayam mati tidak boleh melebihi jumlah ayam yang ada.',
        ], 422);
    }

    $validated['user_id']   = $request->user()->id;
    $validated['siklus_id'] = $siklus->id;
    $validated['umur_ayam'] = now()->diffInDays($siklus->tanggal_mulai, true);

    if ($request->hasFile('foto')) {
        $validated['foto'] = $request->file('foto')->store('laporan_kandang', 'public');
    }

    $laporan = LaporanKandang::create($validated);

    return response()->json([
        'message'  => 'Laporan kandang berhasil dibuat',
        'data'     => $laporan,
        'foto_url' => $laporan->foto ? asset('storage/' . $laporan->foto) : null,
    ], 201);
    }

    public function update(Request $request, $id)
    {
        $laporan = LaporanKandang::findOrFail($id);
        if ($laporan->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

	$laporanPertama = LaporanKandang::where('siklus_id', $laporan->siklus_id)
        ->oldest('tanggal')
        ->first();

    	$isLaporanPertama = $laporanPertama && $laporanPertama->id === $laporan->id;

        if (!$isLaporanPertama && $request->has('jumlah_ayam_awal')) {
        	return response()->json([
            	'message' => 'jumlah_ayam_awal tidak bisa diubah manual selain di laporan pertama siklus.',
        	], 422);
    	}

	$rules = [
            'tanggal'          => 'sometimes|required|date',
            'jumlah_ayam_mati' => 'sometimes|required|integer|min:0',
            'rata_rata_bobot'  => 'sometimes|required|numeric|min:0',
            'catatan'          => 'nullable|string',
            'foto'             => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];

	if ($isLaporanPertama) {
        	$rules['jumlah_ayam_awal'] = 'sometimes|required|integer|min:0';
    	}

    	$validated = $request->validate($rules);

        if ($request->hasFile('foto')) {
            if ($laporan->foto) {
                Storage::disk('public')->delete($laporan->foto);
            }
            $validated['foto'] = $request->file('foto')->store('laporan_kandang', 'public');
        }

        $laporan->update($validated);

        return response()->json([
            'message'  => 'Laporan kandang berhasil diupdate',
            'data'     => $laporan,
            'foto_url' => $laporan->foto ? asset('storage/' . $laporan->foto) : null,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $laporan = LaporanKandang::findOrFail($id);
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

