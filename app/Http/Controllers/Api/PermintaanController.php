<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Permintaan;

class PermintaanController extends Controller
{
    
    public function index(Request $request)
    {
        $user = $request->user();

        // kalau admin lihat semua
        if ($user->role == 'admin') {
            $data = Permintaan::all();
        } else {
            // selain admin hanya lihat milik sendiri
            $data = Permintaan::where('user_id', $user->id)->get();
        }

        return response()->json([
            'data' => $data
        ]);
    }

    // CREATE
    public function store(Request $request)
    {
        $request->validate([
            'nama_permintaan' => 'required',
            'tipe' => 'required|in:barang,dana',
            'jumlah' => 'nullable|integer',
            'harga' => 'nullable|numeric',
            'tanggal' => 'required|date'
        ]);

        $data = Permintaan::create([
            'user_id' => $request->user()->id,
            'nama_permintaan' => $request->nama_permintaan,
            'tipe' => $request->tipe,
            'jumlah' => $request->jumlah,
            'harga' => $request->harga,
            'tanggal' => $request->tanggal,
            'status' => 'pending'
        ]);

        return response()->json([
            'message' => 'Permintaan berhasil dibuat',
            'data' => $data
        ]);
    }

    // GET DETAIL
    public function show($id)
    {
        $permintaan = Permintaan::findOrFail($id);

         return response()->json([
            'data' => $permintaan
        ]);
    }

    public function update(Request $request, $id)
{
    $permintaan = Permintaan::findOrFail($id);

        if ($permintaan->user_id != $request->user()->id) {
        return response()->json([
           'message' => 'Akses ditolak'
        ], 403);
    }

    // VALIDASI
    $validated = $request->validate([
        'nama_permintaan' => 'sometimes|required|string|max:255',
        'tipe' => 'sometimes|required|in:barang,dana',
        'jumlah' => 'nullable|integer',
        'harga' => 'nullable|numeric',
        'tanggal' => 'sometimes|required|date'
    ]);

    // UPDATE DATA
    $permintaan->update($validated);

    return response()->json([
        'message' => 'Permintaan berhasil diupdate',
        'data' => $permintaan
    ]);
}

    // DELETE
    public function destroy(Request $request, $id)
{
    $permintaan = Permintaan::findOrFail($id);

    if ($permintaan->user_id != $request->user()->id) {
        return response()->json([
            'message' => 'Akses ditolak'
        ], 403);
    }

    $permintaan->delete();

    return response()->json([
        'message' => 'Permintaan berhasil dihapus'
    ]);
}

    //  UPDATE STATUS (KHUSUS ADMIN)
    public function updateStatus(Request $request, $id)
    {
        $permintaan = Permintaan::findOrFail($id);
        if ($request->user()->role != 'admin' && $permintaan->user_id != $request->user()->id) {
            return response()->json([
                'message' => 'Akses ditolak'
        ], 403);
    }

        $request->validate([
            'status' => 'required|in:pending,disetujui,ditolak'
        ]);

        $permintaan->update([
            'status' => $request->status
        ]);

        return response()->json([
            'message' => 'Status berhasil diupdate',
            'data' => $permintaan
        ]);
    }
}