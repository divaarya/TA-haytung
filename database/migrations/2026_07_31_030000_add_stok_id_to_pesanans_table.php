<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            $table->foreignId('stok_id')
                ->nullable()
                ->after('id')
                ->constrained('stoks')
                ->nullOnDelete();
            $table->string('jenis')->nullable()->after('stok_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('stok_id');
            $table->dropColumn('jenis');
        });
    }
};
