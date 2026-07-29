<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE karyawans MODIFY role ENUM('gudang','kandang','reseller','admin') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE karyawans MODIFY role ENUM('gudang','kandang','reseller') NOT NULL");
    }
};
