<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PesananItem extends Model
{
    protected $fillable = [
        'pesanan_id',
        'stok_id',
        'gudang',
        'jenis',
        'bobot',
        'kuantitas',
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class);
    }

    public function stok()
    {
        return $this->belongsTo(Stok::class);
    }
}
