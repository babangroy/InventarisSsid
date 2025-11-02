<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ssid extends Model
{
    use HasFactory;

    protected $fillable = [
        'ssid',
        'password',
        'lokasi',
        'status',
    ];
}
