<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('karyawans', function (Blueprint $table) {
            $table->id();

            $table->string('nama_lengkap');
            $table->string('nama_panggilan');

            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');

            $table->enum('jenis_kelamin', [
                'L',
                'P'
            ]);

            $table->date('tanggal_bergabung');

            $table->enum('role', [
                'gudang',
                'kandang',
                'reseller'
            ]);

            $table->enum('status', [
                'aktif',
                'cuti'
            ]);

            // khusus reseller
            $table->string('nama_usaha')->nullable();
            $table->text('alamat_usaha')->nullable();
            $table->string('jenis_usaha')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('karyawans');
    }
};