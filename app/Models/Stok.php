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

    public function pesananItems()
    {
        return $this->hasMany(PesananItem::class);
    }

    /**
     * Tentuin status stok berdasarkan jumlah. Range 50-500 dianggap "aman";
     * di bawah 50 kekurangan stok ("tidak aman"), di atas 500 kebanyakan
     * stok ("waspada" — risiko basi/kapasitas gudang buat produk daging).
     * Ambang batas ini masih perkiraan awal, sesuaikan sama kebutuhan bisnis
     * kamu. Dipakai bareng oleh LaporanGudangController & PesananController
     * biar aturannya satu tempat.
     */
    public static function tentukanStatus(int $jumlah): string
    {
        if ($jumlah < 50) return 'tidak aman';
        if ($jumlah > 500) return 'waspada';
        return 'aman';
    }
}