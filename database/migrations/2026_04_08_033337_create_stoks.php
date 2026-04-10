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
        Schema::create('stoks', function (Blueprint $table) {
            $table->id();
        
            $table->string('jenis'); // whole, parting
            $table->float('berat_per_item'); // 0,6-1,0kg
        
            $table->integer('jumlah_stok');
        
            $table->float('estimasi_total_berat');
        
            $table->date('tanggal_update');
        
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // PIC
            $table->enum('status', ['aman','menipis','habis']);
        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stoks');
    }
};
