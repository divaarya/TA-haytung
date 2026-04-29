<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
    'user_id',
    'role',
    'nama_kegiatan',
    'deskripsi',
    'jumlah',
    'status',
    'tanggal'
];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}