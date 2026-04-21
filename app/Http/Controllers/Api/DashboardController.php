<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Models\Permintaan;
use App\Models\Event;

class DashboardController extends Controller
{
    public function index()
    {
        return response()->json([
            'total_laporan' => Laporan::count(),
            'total_event' => Event::count(),
            'total_permintaan' => Permintaan::count(),

            'ayam_mati' => (int) Laporan::sum('jumlah_ayam_mati'),
            'ayam_hidup' => (int) Laporan::sum('jumlah_ayam_hidup'),

            'permintaan_pending' => Permintaan::where('status','pending')->count(),
            'event_pending' => Event::where('status','pending')->count(),
        ]);
    }
}
