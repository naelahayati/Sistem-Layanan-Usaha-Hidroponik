<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

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

    protected $appends = ['image_url'];

    protected function imageUrl(): Attribute
    {
        return Attribute::get(function () {
            if (!$this->image) {
                return asset('image/kunjungantk.jpeg');
            }
            // Kalau sudah full URL Cloudinary, return langsung
            if (str_starts_with($this->image, 'http')) {
                return $this->image;
            }
            // Fallback untuk data lama yang masih pakai Storage lokal
            return asset('storage/' . $this->image);
        });
    }
}
