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
        'tanggal'
    ];
}
