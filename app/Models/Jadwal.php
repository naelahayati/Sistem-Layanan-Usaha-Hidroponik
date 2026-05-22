<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    protected $fillable = [
        'title',
        'kategori',
        'deskripsi',
        'start_date',
        'end_date',
    ];
}
