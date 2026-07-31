<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permintaan extends Model
{
    protected $fillable = [
        'user_id',
        'nama_permintaan',
        'tipe',
        'jumlah',
        'harga',
        'status',
        'alasan_tolak',
        'tanggal',
        'tempat_pendistribusian',
        'catatan',
        'foto'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}