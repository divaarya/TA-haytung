<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LaporanKandang;

class LaporanKandangSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'user_id'          => 2,
                'tanggal_mulai'    => '2025-12-10',
                'tanggal_selesai'  => '2025-12-30',
                'jumlah_ayam_awal' => 500,
                'jumlah_ayam_mati' => 12,
                'umur_ayam'        => 70,
                'rata_rata_bobot'  => 0.8,
                'catatan'          => 'Ayam dalam kondisi sehat, bobot rata-rata minggu ini 0.8kg.',
                'foto'             => null,
            ],
            [
                'user_id'          => 2,
                'tanggal_mulai'    => '2025-12-10',
                'tanggal_selesai'  => '2025-12-30',
                'jumlah_ayam_awal' => 480,
                'jumlah_ayam_mati' => 8,
                'umur_ayam'        => 70,
                'rata_rata_bobot'  => 0.75,
                'catatan'          => 'Ada beberapa ayam yang kurang nafsu makan.',
                'foto'             => null,
            ],
            [
                'user_id'          => 2,
                'tanggal_mulai'    => '2025-12-10',
                'tanggal_selesai'  => '2025-12-30',
                'jumlah_ayam_awal' => 460,
                'jumlah_ayam_mati' => 5,
                'umur_ayam'        => 70,
                'rata_rata_bobot'  => 0.8,
                'catatan'          => null,
                'foto'             => null,
            ],
            [
                'user_id'          => 2,
                'tanggal_mulai'    => '2025-12-10',
                'tanggal_selesai'  => '2025-12-30',
                'jumlah_ayam_awal' => 450,
                'jumlah_ayam_mati' => 3,
                'umur_ayam'        => 70,
                'rata_rata_bobot'  => 0.7,
                'catatan'          => 'Siap panen minggu depan.',
                'foto'             => null,
            ],
        ];

        foreach ($data as $item) {
            LaporanKandang::create($item);
        }
    }
}