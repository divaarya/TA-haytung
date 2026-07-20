<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LaporanGudang;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class LaporanGudangController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'gudang') {
            $data = LaporanGudang::with('items')->where('user_id', $user->id)->latest()->get();
        } else {
            $data = LaporanGudang::with(['user', 'items'])->latest()->get();
        }

        return response()->json(['data' => $data]);
    }

    public function show($id)
    {
        $laporan = LaporanGudang::with('items')->findOrFail($id);

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
            'tanggal'                  => 'required|date',
            'tempat_pendistribusian'   => 'required|in:Bogor,Depok',
            'catatan'                  => 'nullable|string',
            'foto'                     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'items'                    => 'required|array|min:1',
            'items.*.jenis'            => 'required|string|max:50',
            'items.*.bobot'            => 'required|numeric|min:0.5|max:1.2',
            'items.*.jumlah'           => 'required|integer|min:1',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('laporan_gudang', 'public');
        }

        $laporan = DB::transaction(function () use ($validated, $request, $fotoPath) {
            $laporan = LaporanGudang::create([
                'user_id'                => $request->user()->id,
                'tanggal'                => $validated['tanggal'],
                'tempat_pendistribusian' => $validated['tempat_pendistribusian'],
                'catatan'                => $validated['catatan'] ?? null,
                'foto'                   => $fotoPath,
            ]);

            foreach ($validated['items'] as $item) {
                $laporan->items()->create($item);
            }

            return $laporan;
        });

        $laporan->load('items');

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
            'tanggal'                  => 'sometimes|required|date',
            'tempat_pendistribusian'   => 'sometimes|required|in:Bogor,Depok',
            'catatan'                  => 'nullable|string',
            'foto'                     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'items'                    => 'sometimes|required|array|min:1',
            'items.*.jenis'            => 'required_with:items|string|max:50',
            'items.*.bobot'            => 'required_with:items|numeric|min:0.5|max:1.2',
            'items.*.jumlah'           => 'required_with:items|integer|min:1',
        ]);

        if ($request->hasFile('foto')) {
            if ($laporan->foto) {
                Storage::disk('public')->delete($laporan->foto);
            }
            $validated['foto'] = $request->file('foto')->store('laporan_gudang', 'public');
        }

        DB::transaction(function () use ($laporan, $validated) {
            $laporan->update(collect($validated)->except('items')->toArray());

            if (isset($validated['items'])) {
                $laporan->items()->delete();
                foreach ($validated['items'] as $item) {
                    $laporan->items()->create($item);
                }
            }
        });

        $laporan->load('items');

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

        $laporan->delete(); // items ikut kehapus otomatis karena ON DELETE CASCADE

        return response()->json(['message' => 'Laporan gudang berhasil dihapus']);
    }
}
