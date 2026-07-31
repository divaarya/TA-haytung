<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LaporanGudang;
use App\Models\Stok;
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
            'tempat_pendistribusian'   => 'required|exists:gudangs,nama',
            'catatan'                  => 'nullable|string',
            'foto'                     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'items'                    => 'required|array|min:1',
            'items.*.jenis'            => 'required|string|max:50',
            'items.*.bobot'            => 'required|numeric|min:0.01',
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
                $this->tambahStok($item, $request->user()->id, $validated['tempat_pendistribusian']);
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

    /**
     * Tambahin/update stok di tabel `stoks` berdasarkan item laporan gudang.
     * Dicocokkan berdasarkan `jenis`, `berat_per_item` (dibulatkan 2 desimal)
     * DAN `gudang` — jenis & berat yang sama tapi gudang beda (misal Whole
     * 1,5 kg di Bogor vs Whole 1,5 kg di Depok) dianggap baris stok yang
     * BEDA, karena fisiknya memang disimpan di lokasi yang beda. Kalau belum
     * ada baris yang cocok persis, bikin baris baru; kalau sudah ada,
     * jumlah_stok & estimasi_total_berat diakumulasi.
     */
    private function tambahStok(array $item, int $userId, string $gudang): void
    {
        $bobot = $item['bobot'];

        $existing = Stok::whereRaw('LOWER(jenis) = ?', [strtolower($item['jenis'])])
            ->whereRaw('ROUND(berat_per_item, 2) = ROUND(?, 2)', [$bobot])
            ->where('gudang', $gudang)
            ->first();

        if ($existing) {
            $jumlahBaru = $existing->jumlah_stok + $item['jumlah'];
            $totalBeratBaru = $existing->estimasi_total_berat + ($bobot * $item['jumlah']);

            $existing->update([
                'jumlah_stok'          => $jumlahBaru,
                'estimasi_total_berat' => $totalBeratBaru,
                'tanggal_update'       => now()->toDateString(),
                'status'               => Stok::tentukanStatus($jumlahBaru),
            ]);
        } else {
            Stok::create([
                'user_id'              => $userId,
                'jenis'                => $item['jenis'],
                'gudang'               => $gudang,
                'berat_per_item'       => $bobot,
                'jumlah_stok'          => $item['jumlah'],
                'estimasi_total_berat' => $bobot * $item['jumlah'],
                'tanggal_update'       => now()->toDateString(),
                'status'               => Stok::tentukanStatus($item['jumlah']),
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $laporan = LaporanGudang::findOrFail($id);

        if ($laporan->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $validated = $request->validate([
            'tanggal'                  => 'sometimes|required|date',
            'tempat_pendistribusian'   => 'sometimes|required|exists:gudangs,nama',
            'catatan'                  => 'nullable|string',
            'foto'                     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'items'                    => 'sometimes|required|array|min:1',
            'items.*.jenis'            => 'required_with:items|string|max:50',
            'items.*.bobot'            => 'required_with:items|numeric|min:0.01',
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
