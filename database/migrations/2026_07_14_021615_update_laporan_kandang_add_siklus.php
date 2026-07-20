<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laporan_kandang', function (Blueprint $table) {
            $table->foreignId('siklus_id')->nullable()->after('user_id')->constrained('siklus_kandang');
            $table->date('tanggal')->after('siklus_id');
        });

        Schema::table('laporan_kandang', function (Blueprint $table) {
            $table->dropColumn(['tanggal_mulai', 'tanggal_selesai']);
        });
    }

    public function down(): void
    {
        Schema::table('laporan_kandang', function (Blueprint $table) {
            $table->dropForeign(['siklus_id']);
            $table->dropColumn(['siklus_id', 'tanggal']);
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
        });
    }
};
