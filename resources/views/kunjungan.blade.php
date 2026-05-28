@extends('master')

@section('konten')
<link rel="stylesheet" href="{{ asset('css/stylekunjungan.css?v=1.3') }}">


<main class="kunjungan-page">
    <header class="kunjungan-banner page-header-main">
        <h1>KUNJUNGAN</h1>
        <a href="{{ route('reservasi.riwayat') }}" class="hero-riwayat-link">Cek Riwayat Reservasi <i class="fas fa-arrow-right"></i></a>
    </header>

        <div class="container-kunjungan-list">
            @forelse($kunjungans as $item)
                <section class="kunjungan-item">
                    <div class="kunjungan-photo-box">
                        <img src="{{ $item->image_url }}" alt="{{ $item->name }}">
                    </div>
                    <div class="kunjungan-desc-box">
                        <h2>Kunjungan Class {{ $loop->iteration }}</h2>
                        <h3 style="color: #2d5a27; font-family: 'Times New Roman', serif; font-size: 1.8rem; margin-top: -10px; margin-bottom: 15px;">
                            {{ $item->name }}
                        </h3>
                        <p>
                            {{ $item->description }}
                        </p>
                        <ul style="list-style: none; padding: 0; margin-bottom: 25px; color: #555; line-height: 1.8;">
                            <li><strong>👥 Kapasitas Max:</strong> {{ $item->max_people }} Peserta</li>
                            <li><strong>💳 Pembayaran:</strong> Transfer & Cash</li>
                            <li><strong>💰 Harga:</strong> Rp {{ number_format($item->price, 0, ',', '.') }}/Orang</li>
                        </ul>
                        @if(auth()->check() && auth()->user()->role === 'admin')
                            <a href="#" onclick="if(typeof Swal !== 'undefined') { Swal.fire('Akses Terbatas', 'Anda sedang berada di akun admin. Jika ingin melakukan transaksi harus login menggunakan akun pengguna.', 'warning'); } else { alert('Anda sedang berada di akun admin. Jika ingin melakukan transaksi harus login menggunakan akun pengguna.'); } return false;" class="btn-Daftar">Daftar</a>
                        @else
                            <a href="{{ route('nazfram.reservasi-kunjungan', $item->id) }}" class="btn-Daftar">Daftar</a>
                        @endif
                    </div>
                </section>
            @empty
                <div class="text-center py-5">
                    <p class="text-muted">Belum ada paket kunjungan yang tersedia saat ini.</p>
                </div>
            @endforelse
        </div>

    {{-- Tempat tabel riwayat muncul --}}
    @yield('content')

</main>
@endsection
