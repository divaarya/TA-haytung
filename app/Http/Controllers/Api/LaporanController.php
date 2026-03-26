<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Laporan;

class LaporanController extends Controller
{

   public function update(Request $request, $id)
{
    $laporan = Laporan::findOrFail($id);

    // PROTEKSI (biar user cuma bisa edit miliknya)
    if ($laporan->user_id != $request->user()->id) {
        return response()->json([
            'message' => 'Akses ditolak'
        ], 403);
    }

    // VALIDASI
    $validated = $request->validate([
        'judul' => 'sometimes|required|string|max:255',
        'deskripsi' => 'sometimes|required|string',
        'jenis_laporan' => 'sometimes|required|in:kandang,gudang',
        'tanggal' => 'sometimes|required|date',

        'jumlah_ayam_mati' => 'nullable|integer',
        'jumlah_ayam_hidup' => 'nullable|integer',
        'hari_ke' => 'nullable|integer',
        'estimasi_panen' => 'nullable|date',

        'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
    ]);

    $path = $laporan->foto;

    if ($request->hasFile('foto')) {
        $path = $request->file('foto')->store('laporan', 'public');
    }

    $validated['foto'] = $path;

    // UPDATE
    $laporan->update($validated);

    return response()->json([
        'message' => 'Laporan berhasil diupdate',
        'data' => $laporan,
        'foto_url' => $path ? asset('storage/' . $path) : null
    ]);
}

public function destroy($id)
{
    $laporan = Laporan::findOrFail($id);
    $laporan->delete();

    return response()->json([
        'message' => 'Laporan berhasil dihapus'
    ]);
}

    public function index(Request $request)
{
    $user = $request->user();

    if ($user->role == 'kandang') {
        $data = Laporan::where('jenis_laporan', 'kandang')->get();
    } elseif ($user->role == 'gudang') {
        $data = Laporan::where('jenis_laporan', 'gudang')->get();
    } else {
        $data = Laporan::all();
    }

    return response()->json([
        'data' => $data
    ]);
}

    public function store(Request $request)
{
    $request->validate([
        'judul' => 'required',
        'deskripsi' => 'required',
        'jenis_laporan' => 'required|in:kandang,gudang',
        'tanggal' => 'required|date',

        'jumlah_ayam_mati' => 'nullable|integer',
        'jumlah_ayam_hidup' => 'nullable|integer',
        'hari_ke' => 'nullable|integer',
        'estimasi_panen' => 'nullable|date',

        'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
    ]);

    $path = null;
    if ($request->hasFile('foto')) {
        $path = $request->file('foto')->store('laporan', 'public');
    }

    $laporan = Laporan::create([
        'user_id' => $request->user()->id,
        'judul' => $request->judul,
        'deskripsi' => $request->deskripsi,
        'jenis_laporan' => $request->jenis_laporan,
        'tanggal' => $request->tanggal,

        'jumlah_ayam_mati' => $request->jumlah_ayam_mati,
        'jumlah_ayam_hidup' => $request->jumlah_ayam_hidup,
        'hari_ke' => $request->hari_ke,
        'estimasi_panen' => $request->estimasi_panen,

        'foto' => $path
    ]);

    return response()->json([
        'message' => 'Laporan berhasil dibuat',
        'data' => $laporan
    ]);
}

    public function show($id)
{
    $laporan = Laporan::findOrFail($id);

    return response()->json([
        'data' => $laporan
    ]);
}
}
