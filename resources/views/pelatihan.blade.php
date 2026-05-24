@extends('master')

@section('konten')

    <link rel="stylesheet" href="{{ asset('css/stylepelatihan.css?v=1.3') }}">

    <main class="pelatihan-page">
        <header class="pelatihan-banner page-header-main">
            <h1>MAGANG</h1>
            <a href="{{ route('magang.riwayat') }}" class="hero-riwayat-link">Cek Riwayat Pendaftaran <i class="fas fa-arrow-right"></i></a>
        </header>

        <div class="container-pelatihan-list">
            @forelse($magangs as $item)
                <section class="pelatihan-item">
                    <div class="pelatihan-photo-box">
                        <img src="{{ $item->image ? asset('storage/' . $item->image) : asset('image/magang.jpeg') }}" alt="{{ $item->name }}">
                    </div>
                    <div class="pelatihan-desc-box">
                        <h2>Magang Class {{ $loop->iteration }}</h2>
                        <h3 class="pelatihan-subtitle">{{ $item->name }}</h3>
                        <p>
                            {{ $item->description }}
                        </p>
                        <ul class="pelatihan-info">
                            <li><strong>📍 Tipe:</strong> Magang / Pelatihan</li>
                            <li><strong>💳 Pembayaran:</strong> Transfer & Cash</li>
                            <li><strong>💰 Harga:</strong> {{ $item->price == 0 ? 'Gratis' : 'Rp ' . number_format($item->price, 0, ',', '.') . '/Bulan' }}</li>
                        </ul>
                        @if(auth()->check() && auth()->user()->role === 'admin')
                            <a href="#" onclick="if(typeof Swal !== 'undefined') { Swal.fire('Akses Terbatas', 'Anda sedang berada di akun admin. Jika ingin melakukan transaksi harus login menggunakan akun pengguna.', 'warning'); } else { alert('Anda sedang berada di akun admin. Jika ingin melakukan transaksi harus login menggunakan akun pengguna.'); } return false;" class="btn-Daftar">
                                {{ $item->is_wa_confirmation ? 'Daftar & Konfirmasi WA' : 'Daftar' }}
                            </a>
                        @else
                            <a href="{{ route('nazfram.daftar', $item->id) }}" class="btn-Daftar">
                                {{ $item->is_wa_confirmation ? 'Daftar & Konfirmasi WA' : 'Daftar' }}
                            </a>
                        @endif
                    </div>
                </section>
            @empty
                <div class="text-center py-5">
                    <p class="text-muted">Belum ada paket magang yang tersedia saat ini.</p>
                </div>
            @endforelse
        </div>

        {{-- Tempat tabel riwayat muncul --}}
        @yield('content')

    </main>
@endsection
