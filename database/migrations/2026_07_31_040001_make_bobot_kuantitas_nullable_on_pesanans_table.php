<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * bobot & kuantitas dulunya wajib diisi langsung di pesanan (satu item
     * per pesanan). Sekarang detail item pindah ke tabel pesanan_items biar
     * satu pesanan bisa punya banyak item, jadi dua kolom lama ini nggak
     * lagi diisi untuk pesanan baru — dibikin nullable (bukan dihapus) biar
     * data pesanan lama yang sudah ada nggak hilang.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE pesanans MODIFY bobot DECIMAL(8,2) NULL');
        DB::statement('ALTER TABLE pesanans MODIFY kuantitas INT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE pesanans MODIFY bobot DECIMAL(8,2) NOT NULL');
        DB::statement('ALTER TABLE pesanans MODIFY kuantitas INT NOT NULL');
    }
};
