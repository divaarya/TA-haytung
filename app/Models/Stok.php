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
}