<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'jenis_id',
        'merek_id',
    ];

    public function jenis()
    {
        return $this->belongsTo(Jenis::class);
    }

    public function merek()
    {
        return $this->belongsTo(Merek::class);
    }

    public function barangKeluars()
    {
        return $this->hasMany(BarangKeluar::class);
    }
}
