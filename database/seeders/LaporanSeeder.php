<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Laporan;

class LaporanSeeder extends Seeder
{

    public function run(): void
{
    Laporan::create([
        'user_id' => 1,
        'judul' => 'Laporan Kandang',
        'deskripsi' => 'Ayam sakit',
        'jenis_laporan' => 'kandang',
        'tanggal' => now()
    ]);
}
}
