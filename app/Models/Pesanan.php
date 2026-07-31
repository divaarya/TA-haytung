<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    protected $fillable = [
        'nama_pemesan',
        'no_hp',
        'keterangan',
        'total',
        'alamat_pengiriman',
    ];

    public function items()
    {
        return $this->hasMany(PesananItem::class);
    }
}
