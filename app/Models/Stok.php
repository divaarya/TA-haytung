<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stok extends Model
{
    protected $table = 'stoks';

    protected $fillable = [
        'user_id',
        'jenis',
        'berat_per_item',
        'jumlah_stok',
        'estimasi_total_berat',
        'tanggal_update',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pesanans()
    {
        return $this->hasMany(Pesanan::class);
    }

    /**
     * Tentuin status stok berdasarkan jumlah. Ambang batas ini masih tebakan —
     * sesuaikan angkanya sama kebutuhan bisnis kamu. Dipakai bareng oleh
     * LaporanGudangController & PesananController biar aturannya satu tempat.
     */
    public static function tentukanStatus(int $jumlah): string
    {
        if ($jumlah <= 0) return 'habis';
        if ($jumlah < 20) return 'menipis';
        return 'aman';
    }
}