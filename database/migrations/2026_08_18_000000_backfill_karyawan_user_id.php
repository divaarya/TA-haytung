<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Kolom `karyawans.user_id` ditambah belakangan (lihat
     * 2026_07_29_000000_add_user_id_to_karyawans_table) tanpa backfill, jadi
     * baris karyawan yang dibuat sebelum migration itu ada NULL user_id-nya.
     * Akibatnya `User::karyawan()` (hasOne via user_id) balikin null buat
     * akun-akun lama, dan detail profil (tempat lahir, tanggal lahir, jenis
     * kelamin, status, tanggal bergabung) nggak pernah muncul di app HayCrew
     * walau datanya sebenernya ada di tabel karyawans.
     *
     * Di-link balik lewat pencocokan nama (karyawans nggak nyimpen email),
     * dan cuma di-update kalau nama itu cocok PERSIS SATU user — biar nggak
     * salah tempel kalau ada nama kembar.
     */
    public function up(): void
    {
        DB::statement("
            UPDATE karyawans k
            SET k.user_id = (
                SELECT u.id FROM users u WHERE u.name = k.nama_lengkap
            )
            WHERE k.user_id IS NULL
            AND (SELECT COUNT(*) FROM users u WHERE u.name = k.nama_lengkap) = 1
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Data-only backfill — nggak ada state skema buat di-rollback.
    }
};
