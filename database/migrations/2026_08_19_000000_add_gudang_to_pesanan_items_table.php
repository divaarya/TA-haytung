<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Sama seperti `jenis`/`bobot`, gudang asal item pesanan di-snapshot di
     * sini (bukan di-join live ke `stoks`) — biar tetap kebaca di detail
     * pesanan lama walau baris stok-nya udah diedit/dihapus belakangan.
     */
    public function up(): void
    {
        Schema::table('pesanan_items', function (Blueprint $table) {
            $table->string('gudang')->nullable()->after('stok_id');
        });
    }

    public function down(): void
    {
        Schema::table('pesanan_items', function (Blueprint $table) {
            $table->dropColumn('gudang');
        });
    }
};
