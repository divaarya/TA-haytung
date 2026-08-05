<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Kolom `tempat_pendistribusian` di `laporan_gudang` ternyata masih ENUM
     * di database (schema drift, ngga pernah kebentuk lewat migration) yang
     * cuma ngizinin sebagian nama gudang lama. Sekarang tempat pendistribusian
     * udah dikelola dinamis lewat tabel `gudangs` (exists:gudangs,nama di
     * validasi), jadi kolomnya perlu dilonggarin ke string biasa supaya nama
     * gudang baru (mis. "Semarang") ngga ditolak MySQL dengan "Data truncated".
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE laporan_gudang MODIFY tempat_pendistribusian VARCHAR(255) NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE laporan_gudang MODIFY tempat_pendistribusian ENUM('Bogor', 'Depok') NULL");
    }
};
