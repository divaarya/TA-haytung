<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    protected $fillable = [
        'nama_pemesan',
        'no_hp',
        'bobot',
        'keterangan',
        'kuantitas',
        'total',
        'alamat_pengiriman',
    ];
}
