<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PengirimanPanen;
use Illuminate\Http\Request;

class PengirimanPanenController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = PengirimanPanen::with(['user', 'siklus', 'validator'])
            ->orderByDesc('tanggal_kirim');

        // Kandang cuma lihat kiriman miliknya sendiri. Gudang & admin perlu
        // lihat semua kiriman kandang manapun buat divalidasi.
        if ($user->role === 'kandang') {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        return response()->json(['data' => $query->get()]);
    }

    // Total ayam hidup yang sudah tervalidasi tiba di gudang (disetujui
    // maupun ditolak/selisih -- dua-duanya tetap ada jumlah_diterima aktual).
    // Belum ada pemisahan per lokasi gudang karena pengiriman panen belum
    // menyimpan gudang tujuan.
    public function summary(Request $request)
    {
        $total = PengirimanPanen::whereIn('status', ['disetujui', 'ditolak'])
            ->sum('jumlah_diterima');

        return response()->json([
            'data' => ['total_ayam_hidup' => (int) $total],
        ]);
    }

    public function show(Request $request, $id)
    {
        $pengiriman = PengirimanPanen::with(['user', 'siklus', 'validator'])->findOrFail($id);

        if ($request->user()->role === 'kandang' && $pengiriman->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        return response()->json(['data' => $pengiriman]);
    }

    // Dipanggil karyawan gudang buat konfirmasi penerimaan kiriman panen.
    public function validasi(Request $request, $id)
    {
        if ($request->user()->role !== 'gudang') {
            return response()->json([
                'message' => 'Akses ditolak, hanya gudang yang bisa validasi penerimaan',
            ], 403);
        }

        $pengiriman = PengirimanPanen::findOrFail($id);

        if ($pengiriman->status !== 'pending') {
            return response()->json([
                'message' => 'Kiriman ini sudah divalidasi sebelumnya',
            ], 422);
        }

        $validated = $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'jumlah_diterima' => 'required_if:status,ditolak|nullable|integer|min:0',
            'keterangan' => 'required_if:status,ditolak|nullable|string',
        ]);

        // Disetujui = gudang terima persis sejumlah yang diklaim kandang.
        // Ditolak = ada selisih, gudang catat angka aktual + alasannya.
        $jumlahDiterima = $validated['status'] === 'disetujui'
            ? $pengiriman->jumlah_dikirim
            : $validated['jumlah_diterima'];

        $pengiriman->update([
            'status' => $validated['status'],
            'jumlah_diterima' => $jumlahDiterima,
            'keterangan' => $validated['keterangan'] ?? null,
            'validated_by' => $request->user()->id,
            'validated_at' => now(),
        ]);

        return response()->json([
            'message' => 'Validasi berhasil disimpan',
            'data' => $pengiriman->fresh(['user', 'siklus', 'validator']),
        ]);
    }
}
