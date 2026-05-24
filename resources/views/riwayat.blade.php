@extends('master')

@section('konten')
<link rel="stylesheet" href="{{ asset('css/pengguna/riwayat_page.css') }}">
<link rel="stylesheet" href="/css/styleriwayat.css">
<main class="produk-page">
    <!-- Hero Header -->
    <header class="produk-hero page-header-sub">
        <div class="hero-content">
            <h1>RIWAYAT KUNJUNGAN</h1>
        </div>
    </header>

    <!-- Tombol Kembali -->
    <div class="action-bar-riwayat">
        <a href="{{ route($backRoute) }}" class="btn-back-riwayat">
            <i class="fas fa-chevron-left"></i>
            <span>Kembali ke Kunjungan</span>
        </a>
    </div>

    <!-- Grid Kartu Pesanan -->
    <div class="container-riwayat">
        @if($data->isEmpty())
            <div class="riwayat-empty">
                <i class="fas fa-calendar-times"></i>
                <p>Belum ada riwayat reservasi kunjungan yang ditemukan.</p>
                <a href="{{ route($backRoute) }}" class="btn-back-riwayat" style="margin-top:20px;">
                    Pesan Sekarang
                </a>
            </div>
        @else
            @foreach($data as $item)
                <div class="order-card">

                    <!-- Header Card -->
                    <div class="order-header">
                        <div class="order-header-left">
                            <span class="order-id">Reservasi #RES-{{ str_pad($item->id_reservasi, 5, '0', STR_PAD_LEFT) }}</span>
                            <span class="order-date">{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y, H:i') }}</span>
                            @if(auth()->user()->role === 'admin')
                                <div class="user-info-badge" style="margin-top: 5px; font-weight: 800; color: #1b3a1a; font-size: 0.9rem;">
                                    <i class="fas fa-user-circle mr-1"></i> User: {{ $item->user_name }}
                                </div>
                            @endif
                        </div>
                        <span class="order-status-badge
                            @if($item->status_pembayaran == 'Menunggu Pembayaran' || $item->status_pembayaran == 'Pending') status-pending
                            @elseif($item->status_pembayaran == 'Lunas' || $item->status_pembayaran == 'Diterima' || $item->status_pembayaran == 'Selesai' || $item->status_pembayaran == 'Berhasil') status-completed
                            @else status-cancelled @endif">
                            {{ $item->status_pembayaran }}
                        </span>
                    </div>

                    <!-- Body Card -->
                    <div class="order-body">
                        <div class="order-summary">
                            <div class="order-label">Total Pembayaran</div>
                            <div class="total-pembayaran">Rp {{ number_format($item->total_harga, 0, ',', '.') }}</div>
                        </div>
                        <div class="order-pengiriman">
                            <i class="fas fa-calendar-check"></i>
                            Jadwal: {{ \Carbon\Carbon::parse($item->tanggal_reservasi)->format('d M Y') }}
                        </div>
                    </div>

                    <!-- Aksi Card -->
                    <div class="order-actions">
                        @if($item->status_pembayaran == 'Menunggu Pembayaran' && $item->metode_pembayaran == 'qris')
                            <a href="{{ route('nazfram.kunjungan.payment', $item->id_reservasi) }}" class="btn-bayar">
                                <i class="fas fa-wallet"></i> Bayar
                            </a>
                        @endif
                        <button class="btn-detail-icon" onclick="openNzModal({{ $item->id_reservasi }})" title="Lihat Detail">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Modal Detail -->
                <div class="nz-modal" id="modalDetail{{ $item->id_reservasi }}">
                    <div class="nz-modal-overlay" onclick="closeNzModal({{ $item->id_reservasi }})"></div>
                    <div class="nz-modal-container">
                        <div class="nz-modal-header">
                            <h5 class="nz-modal-title">Rincian Reservasi #RES-{{ str_pad($item->id_reservasi, 5, '0', STR_PAD_LEFT) }}</h5>
                            <button type="button" class="nz-modal-close" onclick="closeNzModal({{ $item->id_reservasi }})">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="nz-modal-body">
                            <!-- Daftar Item -->
                            <div class="order-items-list">
                                <div class="item-row">
                                    <div style="width:50px;height:50px;background:#e8f0e7;color:#2d5a27;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;">
                                        <i class="fas fa-tree"></i>
                                    </div>
                                    <div class="item-info" style="margin-left: 15px;">
                                        <div class="item-name">{{ $item->paket_name ?? 'Kunjungan / Edukasi' }}</div>
                                        <div class="item-price">{{ $item->jumlah_peserta }} Peserta</div>
                                    </div>
                                    <div class="item-subtotal">Rp {{ number_format($item->total_harga, 0, ',', '.') }}</div>
                                </div>
                            </div>

                            <hr style="border:none; border-top:1px dashed #eef2ee; margin: 20px 0;">

                            <!-- Rincian Pembayaran -->
                            <div class="payment-details">
                                <div class="payment-row grand">
                                    <span>Total Biaya Kunjungan</span>
                                    <span>Rp {{ number_format($item->total_harga, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <hr style="border:none; border-top:1px dashed #eef2ee; margin: 20px 0;">

                            <!-- Info Lainnya -->
                            <div class="info-details">
                                <div>
                                    <i class="fas fa-building" style="color:var(--sage); margin-top:4px; font-size:1.1rem;"></i> 
                                    <div>
                                        <strong>Instansi / Nama Group</strong>
                                        {{ $item->instansi }}
                                    </div>
                                </div>
                                <div>
                                    <i class="fas fa-calendar-alt" style="color:var(--sage); margin-top:4px; font-size:1.1rem;"></i> 
                                    <div>
                                        <strong>Tanggal Pelaksanaan</strong>
                                        {{ \Carbon\Carbon::parse($item->tanggal_reservasi)->format('d M Y') }}
                                    </div>
                                </div>
                                <div>
                                    <i class="fas fa-users" style="color:var(--sage); margin-top:4px; font-size:1.1rem;"></i> 
                                    <div>
                                        <strong>Jumlah Peserta</strong>
                                        {{ $item->jumlah_peserta }} Orang
                                    </div>
                                </div>
                                <div>
                                    <i class="fas fa-wallet" style="color:var(--sage); margin-top:4px; font-size:1.1rem;"></i> 
                                    <div>
                                        <strong>Metode Pembayaran</strong>
                                        {{ strtoupper($item->metode_pembayaran) }}
                                    </div>
                                </div>
                                <div>
                                    <i class="fas fa-info-circle" style="color:var(--sage); margin-top:4px; font-size:1.1rem;"></i> 
                                    <div>
                                        <strong>Status</strong>
                                        {{ $item->status_pembayaran }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div> {{-- Penutup container-riwayat --}}

    <!-- Pagination -->
    <div class="pagination-wrapper">
        @if(!request()->has('all') && $data->total() > 8)
            <div id="viewAllPlaceholder" style="display:none;">
                <li class="page-item">
                    <a href="{{ url()->current() }}?all=1" class="page-link btn-view-all">View All</a>
                </li>
            </div>
            {{ $data->appends(request()->query())->links('pagination::bootstrap-4') }}
        @endif
    </div>
</main>

<script>
    function openNzModal(id) {
        document.getElementById('modalDetail' + id).classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    function closeNzModal(id) {
        document.getElementById('modalDetail' + id).classList.remove('active');
        document.body.style.overflow = 'auto';
    }
    // Pindahkan tombol View All ke tengah pagination
    document.addEventListener('DOMContentLoaded', function() {
        const pagination = document.querySelector('.pagination');
        const viewAllPlaceholder = document.getElementById('viewAllPlaceholder');
        const viewAllLi = viewAllPlaceholder ? viewAllPlaceholder.querySelector('li') : null;
        
        if (viewAllLi) {
            viewAllLi.classList.add('view-all-item');
            if (pagination) {
                // Masukkan sebelum tombol 'Next' (item terakhir)
                const lastItem = pagination.lastElementChild;
                pagination.insertBefore(viewAllLi, lastItem);
            } else {
                // Jika tidak ada pagination (cuma 1 halaman), buat ul baru agar tetap muncul di tengah
                const wrapper = document.querySelector('.pagination-wrapper');
                if (wrapper) {
                    const ul = document.createElement('ul');
                    ul.className = 'pagination';
                    ul.appendChild(viewAllLi);
                    wrapper.appendChild(ul);
                }
            }
        }
    });
</script>

@endsection


