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
        Schema::create('pengiriman_panen', function (Blueprint $table) {
            $table->id();

            $table->foreignId('siklus_id')->constrained('siklus_kandang')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // kandang pengirim

            $table->integer('jumlah_dikirim'); // ekor, diinput kandang
            $table->string('foto'); // bukti foto dari kandang saat kirim
            $table->date('tanggal_kirim');

            $table->enum('status', ['pending', 'disetujui', 'ditolak'])->default('pending');

            // Diisi gudang saat validasi. Kalau disetujui -> otomatis sama dengan
            // jumlah_dikirim. Kalau ditolak -> gudang input angka aktual yang
            // diterima (bisa beda, misal ada yang mati/cacat di jalan) + keterangan.
            $table->integer('jumlah_diterima')->nullable();
            $table->text('keterangan')->nullable();
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('validated_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengiriman_panen');
    }
};
