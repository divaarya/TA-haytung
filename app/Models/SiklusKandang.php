<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiklusKandang extends Model
{
    protected $table = 'siklus_kandang';

    protected $fillable = [
        'user_id',
        'tanggal_mulai',
        'tanggal_panen',
        'status',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_panen' => 'date',
    ];

    public function laporanKandang()
    {
        return $this->hasMany(LaporanKandang::class, 'siklus_id');
    }

    public function pengirimanPanen()
    {
        return $this->hasOne(PengirimanPanen::class, 'siklus_id');
    }

    public function getUmurHariAttribute(): int
    {
        return now()->diffInDays($this->tanggal_mulai, true);
    }

    public function getSudahBolehPanenAttribute(): bool
    {
        return $this->umur_hari >= 70;
    }
}
