<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    protected $fillable = [
        'user_id',
        'nama_lengkap',
        'nama_panggilan',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'tanggal_bergabung',
        'role',
        'gudang',
        'status',
        'nama_usaha',
        'alamat_usaha',
        'jenis_usaha'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}