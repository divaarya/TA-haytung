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
        Schema::create('laporan_kandang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');

            $table->integer('jumlah_ayam_awal');
            $table->integer('jumlah_ayam_mati');
            $table->integer('umur_ayam');
            $table->float('rata_rata_bobot');

            $table->text('catatan')->nullable();
            $table->string('foto')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_kandang');
    }
};
