<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LaporanGudang;

class LaporanGudangSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'user_id'            => 1,
                'tanggal_mulai'      => '2025-12-10',
                'tanggal_selesai'    => '2025-12-30',
                'stok_awal'          => 0,
                'stok_masuk'         => 2,
                'jumlah_daging_jual' => 0,
                'stok_akhir'         => 2,
                'catatan'            => 'Penerimaan pertama dari kandang. 2 ekor bobot 0.8kg.',
                'foto'               => null,
            ],
            [
                'user_id'            => 1,
                'tanggal_mulai'      => '2025-12-10',
                'tanggal_selesai'    => '2025-12-30',
                'stok_awal'          => 2,
                'stok_masuk'         => 3,
                'jumlah_daging_jual' => 2,
                'stok_akhir'         => 3,
                'catatan'            => 'Terima 3 ekor (2x0.8kg, 1x0.7kg). Jual 2 ekor.',
                'foto'               => null,
            ],
            [
                'user_id'            => 1,
                'tanggal_mulai'      => '2025-12-10',
                'tanggal_selesai'    => '2025-12-30',
                'stok_awal'          => 3,
                'stok_masuk'         => 3,
                'jumlah_daging_jual' => 3,
                'stok_akhir'         => 3,
                'catatan'            => 'Terima 3 ekor (2x0.8kg, 1x0.7kg). Jual 3 ekor.',
                'foto'               => null,
            ],
            [
                'user_id'            => 1,
                'tanggal_mulai'      => '2025-12-10',
                'tanggal_selesai'    => '2025-12-30',
                'stok_awal'          => 3,
                'stok_masuk'         => 2,
                'jumlah_daging_jual' => 3,
                'stok_akhir'         => 2,
                'catatan'            => 'Stok mulai menipis. Terima 2 ekor (1x0.8kg, 1x0.7kg).',
                'foto'               => null,
            ],
        ];

        foreach ($data as $item) {
            LaporanGudang::create($item);
        }
    }
}