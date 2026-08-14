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
    public function index(Request $request)
    {
        $query = Pesanan::with('items')->latest();

        if ($request->filled('limit')) {
            $query->limit((int) $request->query('limit'));
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pemesan' => 'required|string|max:255',
            'no_hp' => 'required|string|max:20',
            'keterangan' => 'nullable|string',
            'total' => 'required|numeric|min:0',
            'alamat_pengiriman' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.stok_id' => 'required|exists:stoks,id',
            'items.*.kuantitas' => 'required|integer|min:1',
        ]);

        $pesanan = DB::transaction(function () use ($validated) {
            $pesanan = Pesanan::create([
                'nama_pemesan'      => $validated['nama_pemesan'],
                'no_hp'             => $validated['no_hp'],
                'keterangan'        => $validated['keterangan'] ?? null,
                'total'             => $validated['total'],
                'alamat_pengiriman' => $validated['alamat_pengiriman'],
            ]);

            foreach ($validated['items'] as $item) {
                $this->potongStokUntukItem($pesanan, $item);
            }

            return $pesanan;
        });

        $pesanan->load('items');

        return response()->json([
            'message' => 'Data berhasil ditambah',
            'data' => $pesanan
        ], 201);
    }

    public function show($id)
    {
        return response()->json(
            Pesanan::with('items')->findOrFail($id)
        );
    }

    public function update(Request $request, $id)
    {
        $pesanan = Pesanan::with('items')->findOrFail($id);

        $validated = $request->validate([
            'nama_pemesan' => 'required|string|max:255',
            'no_hp' => 'required|string|max:20',
            'keterangan' => 'nullable|string',
            'total' => 'required|numeric|min:0',
            'alamat_pengiriman' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.stok_id' => 'required|exists:stoks,id',
            'items.*.kuantitas' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($pesanan, $validated) {
            // Kembalikan dulu semua alokasi lama sebelum motong yang baru,
            // biar ganti kuantitas/item tetap konsisten angka stoknya.
            foreach ($pesanan->items as $oldItem) {
                if ($oldItem->stok_id) {
                    $stokLama = Stok::lockForUpdate()->find($oldItem->stok_id);
                    if ($stokLama) {
                        $this->sesuaikanStok($stokLama, $oldItem->kuantitas);
                    }
                }
            }
            $pesanan->items()->delete();

            foreach ($validated['items'] as $item) {
                $this->potongStokUntukItem($pesanan, $item);
            }

            $pesanan->update([
                'nama_pemesan'      => $validated['nama_pemesan'],
                'no_hp'             => $validated['no_hp'],
                'keterangan'        => $validated['keterangan'] ?? null,
                'total'             => $validated['total'],
                'alamat_pengiriman' => $validated['alamat_pengiriman'],
            ]);
        });

        $pesanan->load('items');

        return response()->json([
            'message' => 'Data berhasil diupdate',
            'data' => $pesanan
        ]);
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $pesanan = Pesanan::with('items')->findOrFail($id);

            foreach ($pesanan->items as $item) {
                if ($item->stok_id) {
                    $stok = Stok::lockForUpdate()->find($item->stok_id);
                    if ($stok) {
                        $this->sesuaikanStok($stok, $item->kuantitas);
                    }
                }
            }

            $pesanan->delete(); // items ikut kehapus otomatis karena ON DELETE CASCADE
        });

        return response()->json([
            'message' => 'Data berhasil dihapus'
        ]);
    }

    /**
     * Potong stok buat satu baris item pesanan (dipakai bareng oleh store &
     * update). Ngunci baris stok-nya dulu (lockForUpdate) biar aman kalau
     * ada 2 pesanan masuk stok yang sama secara bersamaan, lalu tolak kalau
     * jumlahnya nggak cukup.
     */
    private function potongStokUntukItem(Pesanan $pesanan, array $item): void
    {
        $stok = Stok::lockForUpdate()->findOrFail($item['stok_id']);

        if ($stok->jumlah_stok < $item['kuantitas']) {
            throw ValidationException::withMessages([
                'items' => "Stok {$stok->jenis} ({$stok->berat_per_item} kg) cuma tersisa {$stok->jumlah_stok} pcs.",
            ]);
        }

        $this->sesuaikanStok($stok, -$item['kuantitas']);

        $pesanan->items()->create([
            'stok_id'   => $stok->id,
            'jenis'     => $stok->jenis,
            'bobot'     => $stok->berat_per_item,
            'kuantitas' => $item['kuantitas'],
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
