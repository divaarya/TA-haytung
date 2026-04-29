<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermintaanSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('permintaans')->insert([
            // ===== RESELLER =====
            [
                'user_id'         => 2,
                'nama_permintaan' => 'Permintaan ambil stok',
                'tipe'            => 'barang',
                'jumlah'          => 50,
                'harga'           => null,
                'status'          => 'pending',
                'alasan_tolak'    => null,
                'tanggal'         => '2025-01-15',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'user_id'         => 2,
                'nama_permintaan' => 'Permintaan tambah kuota reseller',
                'tipe'            => 'dana',
                'jumlah'          => null,
                'harga'           => 5000000.00,
                'status'          => 'pending',
                'alasan_tolak'    => null,
                'tanggal'         => '2025-01-18',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],

            // ===== GUDANG =====
            [
                'user_id'         => 2,
                'nama_permintaan' => 'Pengajuan kirim barang ke depok',
                'tipe'            => 'barang',
                'jumlah'          => 100,
                'harga'           => null,
                'status'          => 'pending',
                'alasan_tolak'    => null,
                'tanggal'         => '2025-01-20',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'user_id'         => 2,
                'nama_permintaan' => 'Permintaan pengadaan rak penyimpanan',
                'tipe'            => 'dana',
                'jumlah'          => null,
                'harga'           => 3500000.00,
                'status'          => 'pending',
                'alasan_tolak'    => null,
                'tanggal'         => '2025-01-22',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],

            // ===== KANDANG =====
            [
                'user_id'         => 3,
                'nama_permintaan' => 'Pengajuan kirim barang ke depok',
                'tipe'            => 'barang',
                'jumlah'          => 200,
                'harga'           => null,
                'status'          => 'pending',
                'alasan_tolak'    => null,
                'tanggal'         => '2025-01-20',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'user_id'         => 3,
                'nama_permintaan' => 'Permintaan pakan ayam tambahan',
                'tipe'            => 'barang',
                'jumlah'          => 300,
                'harga'           => null,
                'status'          => 'pending',
                'alasan_tolak'    => null,
                'tanggal'         => '2025-01-20',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'user_id'         => 3,
                'nama_permintaan' => 'Pengajuan dana perbaikan kandang',
                'tipe'            => 'dana',
                'jumlah'          => null,
                'harga'           => 7500000.00,
                'status'          => 'pending',
                'alasan_tolak'    => null,
                'tanggal'         => '2025-01-21',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
        ]);
    }
}