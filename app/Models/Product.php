<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Product extends Model
{
    protected $fillable = ["name", "price", "stock", "image", "description"];

    protected $appends = ['image_url'];

    protected function imageUrl(): Attribute
    {
        return Attribute::get(function () {
            if (!$this->image) {
                return asset('image/5.png');
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
