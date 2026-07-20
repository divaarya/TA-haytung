<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pemesan');
            $table->string('no_hp');
            $table->decimal('bobot', 8, 2);
            $table->text('keterangan')->nullable();
            $table->integer('kuantitas');
            $table->decimal('total', 12, 2);
            $table->text('alamat_pengiriman');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanans');
    }
};