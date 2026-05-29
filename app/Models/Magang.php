<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Magang extends Model
{
    protected $fillable = [
        'name',
        'price',
        'description',
        'image',
        'is_wa_confirmation',
        'show_skill_description',
    ];

    protected $appends = ['image_url'];

    protected function imageUrl(): Attribute
    {
        return Attribute::get(function () {
            if (!$this->image) {
                return asset('image/magang.jpeg');
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
