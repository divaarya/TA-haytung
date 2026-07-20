<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanGudang extends Model
{
    protected $table = 'laporan_gudang';

    protected $fillable = [
        'user_id',
        'tanggal',
        'tempat_pendistribusian',
        'catatan',
        'foto',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(LaporanGudangItem::class);
    }
}
