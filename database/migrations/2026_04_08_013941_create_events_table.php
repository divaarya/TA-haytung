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
    Schema::create('events', function (Blueprint $table) {
    $table->id();

    $table->foreignId('user_id')->constrained()->cascadeOnDelete();

    $table->enum('role', ['kandang','gudang','reseller']);
    $table->string('nama_kegiatan');
    $table->text('deskripsi')->nullable();

    $table->integer('jumlah')->nullable();

    $table->enum('status', ['pending','acc','reject'])->default('pending');

    $table->date('tanggal');

    $table->timestamps();
});
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};