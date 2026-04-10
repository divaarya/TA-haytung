<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Stok;
use Carbon\Carbon;

class StokSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'jenis'                => 'whole',
                'berat_per_item'       => 0.8,
                'jumlah_stok'          => 2,
                'estimasi_total_berat' => 0.8 * 2,
                'tanggal_update'       => Carbon::today()->subDays(6)->toDateString(),
                'status'               => 'aman',
                'user_id'              => 1,
            ],
            [
                'jenis'                => 'whole',
                'berat_per_item'       => 0.8,
                'jumlah_stok'          => 2,
                'estimasi_total_berat' => (0.8 * 2) + (0.7 * 1),
                'tanggal_update'       => Carbon::today()->subDays(4)->toDateString(),
                'status'               => 'aman',
                'user_id'              => 1,
            ],
            [
                'jenis'                => 'whole',
                'berat_per_item'       => 0.7,
                'jumlah_stok'          => 1,
                'estimasi_total_berat' => 0.7 * 1,
                'tanggal_update'       => Carbon::today()->subDays(4)->toDateString(),
                'status'               => 'aman',
                'user_id'              => 1,
            ],

            [
                'jenis'                => 'whole',
                'berat_per_item'       => 0.8,
                'jumlah_stok'          => 2,
                'estimasi_total_berat' => (0.8 * 2) + (0.7 * 1),
                'tanggal_update'       => Carbon::today()->subDays(2)->toDateString(),
                'status'               => 'aman',
                'user_id'              => 1,
            ],
            [
                'jenis'                => 'whole',
                'berat_per_item'       => 0.7,
                'jumlah_stok'          => 1,
                'estimasi_total_berat' => 0.7 * 1,
                'tanggal_update'       => Carbon::today()->subDays(2)->toDateString(),
                'status'               => 'aman',
                'user_id'              => 1,
            ],

            [
                'jenis'                => 'whole',
                'berat_per_item'       => 0.8,
                'jumlah_stok'          => 1,
                'estimasi_total_berat' => (0.8 * 1) + (0.7 * 1),
                'tanggal_update'       => Carbon::today()->toDateString(),
                'status'               => 'menipis',
                'user_id'              => 1,
            ],
            [
                'jenis'                => 'whole',
                'berat_per_item'       => 0.7,
                'jumlah_stok'          => 1,
                'estimasi_total_berat' => 0.7 * 1,
                'tanggal_update'       => Carbon::today()->toDateString(),
                'status'               => 'menipis',
                'user_id'              => 1,
            ],
        ];

        foreach ($data as $item) {
            Stok::create($item);
        }
    }
}