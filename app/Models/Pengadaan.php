<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengadaan extends Model
{
    use HasFactory;

    protected $fillable = [
        'barang_id',
        'merek_id',
        'jenis_id',
        'jumlah_awal',
        'jumlah',
        'tgl_masuk',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function merek()
    {
        return $this->belongsTo(Merek::class);
    }

    public function jenis()
    {
        return $this->belongsTo(Jenis::class);
    }
}
