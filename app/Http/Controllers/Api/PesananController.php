<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\Stok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PesananController extends Controller
{
    public function index()
    {
        return response()->json(Pesanan::with('stok')->latest()->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pemesan' => 'required|string|max:255',
            'no_hp' => 'required|string|max:20',
            'stok_id' => 'required|exists:stoks,id',
            'keterangan' => 'nullable|string',
            'kuantitas' => 'required|integer|min:1',
            'total' => 'required|numeric|min:0',
            'alamat_pengiriman' => 'required|string',
        ]);

        $pesanan = DB::transaction(function () use ($validated) {
            $stok = Stok::lockForUpdate()->findOrFail($validated['stok_id']);

            if ($stok->jumlah_stok < $validated['kuantitas']) {
                throw ValidationException::withMessages([
                    'kuantitas' => "Stok {$stok->jenis} cuma tersisa {$stok->jumlah_stok} pcs.",
                ]);
            }

            $this->sesuaikanStok($stok, -$validated['kuantitas']);

            return Pesanan::create([
                'nama_pemesan'      => $validated['nama_pemesan'],
                'no_hp'             => $validated['no_hp'],
                'stok_id'           => $stok->id,
                'jenis'             => $stok->jenis,
                'bobot'             => $stok->berat_per_item,
                'keterangan'        => $validated['keterangan'] ?? null,
                'kuantitas'         => $validated['kuantitas'],
                'total'             => $validated['total'],
                'alamat_pengiriman' => $validated['alamat_pengiriman'],
            ]);
        });

        return response()->json([
            'message' => 'Data berhasil ditambah',
            'data' => $pesanan
        ], 201);
    }

    public function show($id)
    {
        return response()->json(
            Pesanan::with('stok')->findOrFail($id)
        );
    }

    public function update(Request $request, $id)
    {
        $pesanan = Pesanan::findOrFail($id);

        $validated = $request->validate([
            'nama_pemesan' => 'required|string|max:255',
            'no_hp' => 'required|string|max:20',
            'stok_id' => 'required|exists:stoks,id',
            'keterangan' => 'nullable|string',
            'kuantitas' => 'required|integer|min:1',
            'total' => 'required|numeric|min:0',
            'alamat_pengiriman' => 'required|string',
        ]);

        DB::transaction(function () use ($pesanan, $validated) {
            // Kembaliin dulu alokasi lama (kalau pesanan ini sebelumnya sudah
            // kepotong dari stok tertentu), baru potong alokasi baru — biar
            // ganti kuantitas atau ganti item stok tetap konsisten angkanya.
            if ($pesanan->stok_id) {
                $stokLama = Stok::lockForUpdate()->find($pesanan->stok_id);
                if ($stokLama) {
                    $this->sesuaikanStok($stokLama, $pesanan->kuantitas);
                }
            }

            $stokBaru = Stok::lockForUpdate()->findOrFail($validated['stok_id']);

            if ($stokBaru->jumlah_stok < $validated['kuantitas']) {
                throw ValidationException::withMessages([
                    'kuantitas' => "Stok {$stokBaru->jenis} cuma tersisa {$stokBaru->jumlah_stok} pcs.",
                ]);
            }

            $this->sesuaikanStok($stokBaru, -$validated['kuantitas']);

            $pesanan->update([
                'nama_pemesan'      => $validated['nama_pemesan'],
                'no_hp'             => $validated['no_hp'],
                'stok_id'           => $stokBaru->id,
                'jenis'             => $stokBaru->jenis,
                'bobot'             => $stokBaru->berat_per_item,
                'keterangan'        => $validated['keterangan'] ?? null,
                'kuantitas'         => $validated['kuantitas'],
                'total'             => $validated['total'],
                'alamat_pengiriman' => $validated['alamat_pengiriman'],
            ]);
        });

        return response()->json([
            'message' => 'Data berhasil diupdate',
            'data' => $pesanan
        ]);
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $pesanan = Pesanan::findOrFail($id);

            if ($pesanan->stok_id) {
                $stok = Stok::lockForUpdate()->find($pesanan->stok_id);
                if ($stok) {
                    $this->sesuaikanStok($stok, $pesanan->kuantitas);
                }
            }

            $pesanan->delete();
        });

        return response()->json([
            'message' => 'Data berhasil dihapus'
        ]);
    }

    /**
     * Sesuaikan jumlah_stok & estimasi_total_berat sebesar $delta (negatif =
     * kurangi buat pesanan baru, positif = kembalikan buat pesanan yang
     * diedit/dihapus). estimasi_total_berat dihitung ulang dari
     * berat_per_item x jumlah biar nggak ada drift pembulatan.
     */
    private function sesuaikanStok(Stok $stok, int $delta): void
    {
        $jumlahBaru = max(0, $stok->jumlah_stok + $delta);

        $stok->update([
            'jumlah_stok'          => $jumlahBaru,
            'estimasi_total_berat' => $stok->berat_per_item * $jumlahBaru,
            'tanggal_update'       => now()->toDateString(),
            'status'               => Stok::tentukanStatus($jumlahBaru),
        ]);
    }
}
