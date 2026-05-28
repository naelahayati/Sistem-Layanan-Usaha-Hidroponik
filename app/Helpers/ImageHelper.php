<?php

if (!function_exists('image_url')) {
    /**
     * Generate URL gambar — dari Supabase S3 jika tersedia, fallback ke public/image lokal.
     *
     * Penggunaan di Blade: {{ image_url('logo.png') }}
     * Menggantikan: {{ asset('image/logo.png') }}
     */
    function image_url(string $filename): string
    {
        $baseUrl = env('AWS_URL');

        if ($baseUrl) {
            // Hilangkan trailing slash
            $baseUrl = rtrim($baseUrl, '/');
            return $baseUrl . '/' . ltrim($filename, '/');
        }

        // Fallback ke lokal (development)
        return asset('image/' . ltrim($filename, '/'));
    }
}
