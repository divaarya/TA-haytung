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

        if ($user->role == 'admin') {
            $data = Permintaan::with('user')->get()->map(function ($item) {
                $item->role = $item->user->role;
                return $item;
            });
        } else {
            $data = Permintaan::with('user')
                ->where('user_id', $user->id)
                ->get()
                ->map(function ($item) {
                    $item->role = $item->user->role;
                    return $item;
                });
        }

        return response()->json([
            'data' => $data
        ]);
    }

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

    public function show($id)
    {
        $permintaan = Permintaan::with('user')->findOrFail($id);
        $permintaan->role = $permintaan->user->role;

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

        $validated = $request->validate([
            'nama_permintaan' => 'sometimes|required|string|max:255',
            'tipe' => 'sometimes|required|in:barang,dana',
            'jumlah' => 'nullable|integer',
            'harga' => 'nullable|numeric',
            'tanggal' => 'sometimes|required|date'
        ]);

        $permintaan->update($validated);

        return response()->json([
            'message' => 'Permintaan berhasil diupdate',
            'data' => $permintaan
        ]);
    }

    // DELETE
    public function destroy($id)
    {
        $permintaan = Permintaan::findOrFail($id);
        $permintaan->delete();

    return response()->json([
        'message' => 'Permintaan berhasil dihapus'
    ]);
}

    public function updateStatus(Request $request, $id)
    {
        $permintaan = Permintaan::findOrFail($id);
        if ($request->user()->role != 'admin' && $permintaan->user_id != $request->user()->id) {
            return response()->json([
                'message' => 'Akses ditolak'
        ], 403);
    }

        $request->validate([
            'status' => 'required|in:pending,disetujui,ditolak',
            'alasan_tolak' => 'nullable|string'
        ]);

        $permintaan->update([
            'status' => $request->status,
            'alasan_tolak' => $request->alasan_tolak
        ]);

        return response()->json([
            'message' => 'Status berhasil diupdate',
            'data' => $permintaan
        ]);
    }
}