<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Laporan;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class LaporanController extends Controller
{

    // GET ALL
    public function index(Request $request)
{
    $user = $request->user();
    $query = Laporan::query();

    // filter berdasarkan role
    if ($user->role == 'kandang') {
        $query->where('jenis_laporan', 'kandang');
    } elseif ($user->role == 'gudang') {
        $query->where('jenis_laporan', 'gudang');
    }

    if ($request->dc) {
        $query->where('dc', $request->dc);
    }

    return response()->json([
        'data' => $query->get()
    ]);
}

    // GET DETAIL
    public function show($id)
    {
        $laporan = Laporan::findOrFail($id);

        return response()->json([
            'data' => $laporan,
            'foto_url' => $laporan->foto ? asset('storage/' . $laporan->foto) : null
        ]);
    }

    // POST
public function store(Request $request)
{
    $validated = $request->validate([
        'judul' => 'required|string|max:255',
        'deskripsi' => 'required|string',
        'jenis_laporan' => 'required|in:kandang,gudang',
        'tanggal' => 'required|date',

        'dc' => 'required|in:kandang,gudang,reseller',

        'jumlah_ayam_mati' => 'nullable|integer',
        'jumlah_ayam_hidup' => 'nullable|integer',
        'hari_ke' => 'nullable|integer',
        'estimasi_panen' => 'nullable|date',

        'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
    ]);

    if (isset($validated['hari_ke']) && $validated['hari_ke'] <= 30) {
    $validated['estimasi_panen'] = Carbon::now()->addDays(30 - $validated['hari_ke']);
}

    // upload foto
    if ($request->hasFile('foto')) {
        $validated['foto'] = $request->file('foto')->store('laporan', 'public');
    }

    // tambah user_id
    $validated['user_id'] = $request->user()->id;

    $laporan = Laporan::create($validated);

    return response()->json([
        'message' => 'Laporan berhasil dibuat',
        'data' => $laporan,
        'foto_url' => $laporan->foto ? asset('storage/' . $laporan->foto) : null
    ]);
}

    // UPDATE
   public function update(Request $request, $id)
{
    $laporan = Laporan::findOrFail($id);

    // proteksi user
    if ($laporan->user_id != $request->user()->id) {
        return response()->json([
            'message' => 'Akses ditolak'
        ], 403);
    }

    $validated = $request->validate([
        'judul' => 'sometimes|required|string|max:255',
        'deskripsi' => 'sometimes|required|string',
        'jenis_laporan' => 'sometimes|required|in:kandang,gudang',
        'tanggal' => 'sometimes|required|date',

        'dc' => 'sometimes|required|in:kandang,gudang,reseller',

        'jumlah_ayam_mati' => 'nullable|integer',
        'jumlah_ayam_hidup' => 'nullable|integer',
        'hari_ke' => 'nullable|integer',
        'estimasi_panen' => 'nullable|date',

        'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
    ]);

    if (!empty($validated['hari_ke'])) {
    $validated['estimasi_panen'] = Carbon::now()->addDays(30 - $validated['hari_ke']);
}

    // handle foto
    if ($request->hasFile('foto')) {

        if ($laporan->foto && \Storage::disk('public')->exists($laporan->foto)) {
            \Storage::disk('public')->delete($laporan->foto);
        }

        $validated['foto'] = $request->file('foto')->store('laporan', 'public');
    }

    $laporan->update($validated);

    return response()->json([
        'message' => 'Laporan berhasil diupdate',
        'data' => $laporan,
        'foto_url' => $laporan->foto ? asset('storage/' . $laporan->foto) : null
    ]);
}

    // DELETE
    public function destroy(Request $request, $id)
    {
        $laporan = Laporan::findOrFail($id);

        //  proteksi user
        if ($laporan->user_id != $request->user()->id) {
            return response()->json([
                'message' => 'Akses ditolak'
            ], 403);
        }

        // hapus foto 
        if ($laporan->foto && Storage::disk('public')->exists($laporan->foto)) {
            Storage::disk('public')->delete($laporan->foto);
        }

        $laporan->delete();

        return response()->json([
            'message' => 'Laporan berhasil dihapus'
        ]);
    }
}