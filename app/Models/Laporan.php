<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Laporan extends Model
{
    use HasFactory;

    protected $fillable = [
    'user_id',
    'judul',
    'deskripsi',
    'jenis_laporan',
    'dc',
    'jumlah_ayam_mati',
    'jumlah_ayam_hidup',
    'hari_ke',
    'estimasi_panen',
    'foto',
    'tanggal'
];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}