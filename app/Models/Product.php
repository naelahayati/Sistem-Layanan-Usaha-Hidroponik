<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = ["name", "price", "stock", "image", "description"];

    /**
     * Properti tambahan yang akan disertakan saat model dikonversi ke Array/JSON.
     */
    protected $appends = ['image_url'];

    /**
     * Accessor cerdas untuk mendapatkan URL gambar produk secara otomatis.
     * Mendeteksi keberadaan file fisik dan memberikan fallback jika gambar tidak ditemukan.
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::get(function () {
            if (!$this->image) {
                return asset('image/produk.webp');
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

            return asset('image/produk.webp');
        });
    }
}
