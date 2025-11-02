<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangDitarik extends Model
{
    use HasFactory;

    protected $fillable = [
        'barang_id',
        'sn',
        'asal',
        'alasan',
        'kondisi',
        'tanggal_tarik',
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
