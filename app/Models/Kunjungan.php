<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class Kunjungan extends Model
{
    protected $fillable = [
        'name',
        'price',
        'min_people',
        'max_people',
        'description',
        'image',
    ];

    /**
     * Properti tambahan yang akan disertakan saat model dikonversi ke Array/JSON.
     */
    protected $appends = ['image_url'];

    /**
     * Accessor cerdas untuk mendapatkan URL gambar kunjungan.
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::get(function () {
            if (!$this->image) return asset('image/5.png');

            if (str_starts_with($this->image, 'http')) return $this->image;

            $path = ltrim($this->image, '/');
            if (Storage::disk('public')->exists($path)) {
                return asset('storage/' . $path);
            }

            return asset('image/5.png');
        });
    }
}
