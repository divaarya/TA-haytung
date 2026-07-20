<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanKandang extends Model
{
    protected $table = 'laporan_kandang';

    protected $fillable = [
    	'user_id',
    	'siklus_id',
    	'tanggal',
    	'jumlah_ayam_awal',
    	'jumlah_ayam_mati',
    	'umur_ayam',
    	'rata_rata_bobot',
    	'catatan',
    	'foto',
    ];

    public function siklus()
    {
    	return $this->belongsTo(SiklusKandang::class, 'siklus_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
