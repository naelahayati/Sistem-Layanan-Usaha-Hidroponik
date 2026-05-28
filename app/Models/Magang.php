<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

    /**
     * Properti tambahan yang akan disertakan saat model dikonversi ke Array/JSON.
     */
    protected $appends = ['image_url'];

    /**
     * Accessor cerdas untuk mendapatkan URL gambar magang.
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::get(function () {
            if (!$this->image) {
                return asset('image/magang.jpeg');
            }

            if (str_starts_with($this->image, 'http')) {
                return $this->image;
            }

            $path = ltrim($this->image, '/');
            $normalizedPath = Str::startsWith($path, 'storage/')
                ? Str::after($path, 'storage/')
                : $path;

            if (Str::startsWith($path, 'image/')) {
                return asset($path);
            }

            if (Storage::disk('public')->exists($normalizedPath)) {
                return asset('storage/' . $normalizedPath);
            }

            return asset('image/magang.jpeg');
        });
    }
}
