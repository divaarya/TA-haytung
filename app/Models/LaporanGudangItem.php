<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanGudangItem extends Model
{
    protected $table = 'laporan_gudang_items';

    protected $fillable = ['laporan_gudang_id', 'jenis', 'bobot', 'jumlah'];

    public function laporanGudang()
    {
        return $this->belongsTo(LaporanGudang::class);
    }
}
