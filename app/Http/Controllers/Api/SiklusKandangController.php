<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SiklusKandang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiklusKandangController extends Controller
{
    public function aktif(Request $request)
    {
        $query = SiklusKandang::where('status', 'berjalan');

        // Kandang cuma boleh lihat siklus miliknya sendiri. Role lain (admin,
        // dsb) butuh siklus aktif secara sistem buat nampilin Total Ayam Hidup
        // di web — kalau ikut di-scope ke Auth::id() punya admin, hasilnya
        // selalu null karena siklus itu dibuat atas nama user kandang, bukan admin.
        if ($request->user()->role === 'kandang') {
            $query->where('user_id', Auth::id());
        }

        $siklus = $query->latest('tanggal_mulai')->first();

        if (!$siklus) {
            return response()->json([
                'message' => 'Belum ada siklus aktif',
                'data' => null,
            ], 200);
        }

        return response()->json([
            'data' => [
                'id' => $siklus->id,
                'tanggal_mulai' => $siklus->tanggal_mulai->format('Y-m-d'),
                'status' => $siklus->status,
                'umur_hari' => $siklus->umur_hari,
                'sudah_boleh_panen' => $siklus->sudah_boleh_panen,
            ],
        ]);
    }

   
    public function store(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => 'required|date',
        ]);

        $adaSiklusAktif = SiklusKandang::where('user_id', Auth::id())
            ->where('status', 'berjalan')
            ->exists();

        if ($adaSiklusAktif) {
            return response()->json([
                'message' => 'Masih ada siklus yang berjalan. Tandai panen dulu sebelum mulai siklus baru.',
            ], 422);
        }

        $siklus = SiklusKandang::create([
            'user_id' => Auth::id(),
            'tanggal_mulai' => $request->tanggal_mulai,
            'status' => 'berjalan',
        ]);

        return response()->json([
            'message' => 'Siklus baru berhasil dimulai',
            'data' => $siklus,
        ], 201);
    }

    public function tandaiPanen(Request $request, $id)
    {
        $siklus = SiklusKandang::where('user_id', Auth::id())->findOrFail($id);

        if ($siklus->status === 'panen') {
            return response()->json(['message' => 'Siklus ini sudah ditandai panen'], 422);
        }

        $umurHari = now()->diffInDays($siklus->tanggal_mulai, true);

        if ($umurHari < 70) {
            $request->validate([
                'catatan_panen_dini' => 'required|string',
            ], [
                'catatan_panen_dini.required' => 'Umur ayam belum 70 hari, wajib isi alasan panen dini.',
            ]);
        }

        $siklus->update([
            'tanggal_panen' => now(),
            'status' => 'panen',
        ]);

        return response()->json([
            'message' => 'Siklus berhasil ditandai panen',
            'data' => $siklus,
        ]);
    }
}
