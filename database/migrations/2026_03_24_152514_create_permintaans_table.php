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
        Schema::create('permintaans', function (Blueprint $table) {
    $table->id();

    $table->foreignId('user_id')->constrained()->cascadeOnDelete();

    $table->string('nama_permintaan'); 
    $table->enum('tipe', ['barang', 'dana']);
    $table->integer('jumlah')->nullable();
    $table->decimal('harga', 12, 2)->nullable();

    $table->enum('status', ['pending', 'disetujui', 'ditolak'])->default('pending');

    $table->date('tanggal');

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permintaans');
    }
};
