<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengirimanPanen extends Model
{
    protected $table = 'pengiriman_panen';

    protected $fillable = [
        'siklus_id',
        'user_id',
        'jumlah_dikirim',
        'foto',
        'tanggal_kirim',
        'status',
        'jumlah_diterima',
        'keterangan',
        'validated_by',
        'validated_at',
    ];

    protected $casts = [
        'tanggal_kirim' => 'date',
        'validated_at' => 'datetime',
    ];

    protected $appends = [
        'foto_url',
    ];

    public function getFotoUrlAttribute()
    {
        return $this->foto ? asset('storage/' . $this->foto) : null;
    }

    public function siklus()
    {
        return $this->belongsTo(SiklusKandang::class, 'siklus_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }
}
