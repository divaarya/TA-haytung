<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanGudang extends Model
{
    protected $table = 'laporan_gudang';

    protected $fillable = [
        'user_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'stok_awal',
        'stok_masuk',
        'jumlah_daging_jual',
        'stok_akhir',
        'catatan',
        'foto',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}