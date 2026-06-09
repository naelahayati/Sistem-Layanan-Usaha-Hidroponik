@extends('master')

@section('konten')

    <link rel="stylesheet" href="/css/styleproduk.css">

    <main class="produk-page">
        <!-- Hero Section dengan Foto Hidroponik & Gradasi Mewah -->
        <header class="produk-hero page-header-main">
            <div class="hero-content">
                <h1>PRODUK</h1>
                @if(auth()->check() && auth()->user()->role === 'admin')
                    <a href="#" onclick="if(typeof Swal !== 'undefined') { Swal.fire('Akses Terbatas', 'Anda sedang berada di akun admin. Jika ingin melakukan transaksi harus login menggunakan akun pengguna.', 'warning'); } else { alert('Anda sedang berada di akun admin. Jika ingin melakukan transaksi harus login menggunakan akun pengguna.'); } return false;" class="hero-beli-link">beli produk <i class="fas fa-arrow-right"></i></a>
                @else
                    <a href="{{ route('nazfram.beli-produk') }}" class="hero-beli-link">beli produk <i class="fas fa-arrow-right"></i></a>
                @endif
            </div>
        </header>

        <!-- Info Pengiriman -->
        <div class="info-pengiriman">
            <div class="info-pengiriman-inner">
                <div class="info-item">
                    <i class="fas fa-truck"></i>
                    <div>
                        <span class="info-title">Pengiriman Melon</span>
                        <span class="info-desc">Minimal pembelian <strong>5 kg</strong> untuk layanan antar</span>
                    </div>
                </div>
                <div class="info-divider"></div>
                <div class="info-item">
                    <i class="fas fa-leaf"></i>
                    <div>
                        <span class="info-title">Pengiriman Sayuran</span>
                        <span class="info-desc">Minimal pembelian <strong>Rp.150.000</strong> untuk layanan antar</span>
                    </div>
                </div>
                <div class="info-divider"></div>
                <div class="info-item">
                    <i class="fas fa-info-circle"></i>
                    <div>
                        <span class="info-title">Catatan</span>
                        <span class="info-desc">Pembelian di bawah minimal bisa diambil langsung ke lokasi</span>
                    </div>
                </div>
            </div>
        </div>


        <!-- Product Grid: Card Bernapas dengan Soft Shadow -->
        <div class="container-produk-list">
            @foreach($produk as $p)
                <div class="produk-item">
                    <!-- Foto Produk -->
                        <div class="produk-photo-box">
                            <img src="{{ $p->image_url }}" alt="{{ $p->name }}" onload="this.classList.add('loaded')">
                        </div>

                    <!-- Informasi Produk -->
                    <div class="produk-desc-box">
                        <h2>{{ strtoupper($p->name) }}</h2>
                        <div class="price-tag">Rp {{ number_format($p->price, 0, ',', '.') }}</div>
                        <div class="stock-tag">Stok Tersedia: {{ $p->stock }}</div>
                        <p class="produk-deskripsi">{{ Str::limit($p->description, 100) }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </main>

    <script src="/js/scriptproduk.js"></script>

@endsection
