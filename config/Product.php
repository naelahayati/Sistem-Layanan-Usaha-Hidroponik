<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

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
            // Jika kolom image kosong
            if (!$this->image) {
                return asset('image/5.png'); // Sesuaikan dengan placeholder yang Anda miliki
            }

            // Cek apakah path di database sudah memiliki prefix 'http' (misal image hasil seeder/external)
            if (str_starts_with($this->image, 'http')) {
                return $this->image;
            }

            // Pastikan tidak ada double slash saat pengecekan
            $path = ltrim($this->image, '/');

            // Cek apakah file ada di disk public
            if (Storage::disk('public')->exists($path)) {
                return asset('storage/' . $path);
            }

            // Jika file tidak ditemukan secara fisik, berikan placeholder
            return asset('image/5.png');
        });
    }
}
