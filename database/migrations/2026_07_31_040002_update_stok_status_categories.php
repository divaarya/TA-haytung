<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ganti kategori status stok dari (aman, menipis, habis) — yang cuma
     * mikirin kekurangan — ke (aman, waspada, tidak aman), yang juga
     * mikirin kelebihan stok (risiko basi / kapasitas gudang buat produk
     * daging). Widen enum dulu biar data lama (menipis/habis) masih valid
     * pas di-convert, baru dipersempit ke 3 kategori final.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE stoks MODIFY status ENUM('aman', 'menipis', 'habis', 'waspada', 'tidak aman') NOT NULL");
        DB::statement("UPDATE stoks SET status = 'tidak aman' WHERE status IN ('menipis', 'habis')");
        DB::statement("ALTER TABLE stoks MODIFY status ENUM('aman', 'waspada', 'tidak aman') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE stoks MODIFY status ENUM('aman', 'waspada', 'tidak aman', 'menipis', 'habis') NOT NULL");
        DB::statement("UPDATE stoks SET status = 'menipis' WHERE status IN ('waspada', 'tidak aman')");
        DB::statement("ALTER TABLE stoks MODIFY status ENUM('aman', 'menipis', 'habis') NOT NULL");
    }
};
