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
        Schema::create('laporan_gudang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');

            $table->integer('stok_awal');
            $table->integer('stok_masuk');
            $table->integer('jumlah_daging_jual');
            $table->integer('stok_akhir')->nullable();

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
        //
    }
};
