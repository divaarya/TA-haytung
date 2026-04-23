<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanKandang extends Model
{
    protected $table = 'laporan_kandang';

    protected $fillable = [
        'user_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'jumlah_ayam_awal',
        'jumlah_ayam_mati',
        'umur_ayam',
        'rata_rata_bobot',
        'catatan',
        'foto',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}