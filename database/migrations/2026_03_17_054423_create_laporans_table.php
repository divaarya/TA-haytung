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
       Schema::create('laporans', function (Blueprint $table) {
    $table->id();

    $table->foreignId('user_id')->constrained()->cascadeOnDelete();

    $table->string('judul');
    $table->text('deskripsi');

    $table->enum('jenis_laporan', ['kandang','gudang']);

    $table->enum('dc', ['kandang','gudang','reseller']);

    $table->integer('jumlah_ayam_mati')->nullable();
    $table->integer('jumlah_ayam_hidup')->nullable();
    $table->integer('hari_ke')->nullable();
    $table->date('estimasi_panen')->nullable();
    $table->string('foto')->nullable();

    $table->date('tanggal');

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporans');
    }
};
