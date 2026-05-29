@extends('master')

@section('konten')

<link rel="stylesheet" href="/css/styleriwayat.css">

<main class="produk-page">
    <!-- Hero Header -->
    <header class="produk-hero page-header-sub">
        <div class="hero-content">
            <h1>RIWAYAT</h1>
        </div>
    </header>

    <!-- Tombol Kembali -->
    <div class="action-bar-riwayat">
        <a href="{{ route('nazfram.produk') }}" class="btn-back-riwayat">
            <i class="fas fa-chevron-left"></i>
            <span>Kembali ke Produk</span>
        </a>
    </div>

    <!-- Grid Kartu Pesanan -->
    <div class="container-riwayat">
        @if($orders->isEmpty())
            <div class="riwayat-empty">
                <i class="fas fa-receipt"></i>
                <p>Anda belum memiliki riwayat pesanan.</p>
                <a href="{{ route('nazfram.produk') }}" class="btn-back-riwayat" style="margin-top:20px;">
                    Mulai Belanja Sekarang
                </a>
            </div>
        @else
            @foreach($orders as $order)
                <div class="order-card">

                    <!-- Header Card -->
                    <div class="order-header">
                        <div class="order-header-left">
                            <span class="order-id">Order #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</span>
                            <span class="order-date">{{ $order->created_at->format('d M Y, H:i') }}</span>
                        </div>
                        <span class="order-status-badge
                            @if($order->status == 'Menunggu Pembayaran') status-pending
                            @elseif($order->status == 'Lunas') status-completed
                            @elseif($order->status == 'Diproses') status-processed
                            @elseif($order->status == 'Pesanan Siap Diambil') status-shipped
                            @elseif($order->status == 'Dikirim') status-shipped
                            @elseif($order->status == 'Selesai') status-completed
                            @else status-cancelled @endif">
                            {{ $order->status }}
                        </span>
                    </div>

                    <!-- Body Card -->
                    <div class="order-body">
                        <div class="order-summary">
                            <div class="order-label">Total Pembayaran</div>
                            <div class="total-pembayaran">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</div>
                        </div>
                        <div class="order-pengiriman">
                            <i class="fas fa-{{ $order->metode_pengiriman == 'pengantaran' ? 'truck' : 'store' }}"></i>
                            {{ $order->metode_pengiriman == 'pengantaran' ? 'Dikirim ke Alamat' : 'Ambil di Kebun' }}
                        </div>
                    </div>

                    <!-- Aksi Card -->
                    <div class="order-actions">
                        @if($order->status == 'Menunggu Pembayaran' && $order->metode_pembayaran == 'qris')
                            <a href="{{ route('nazfram.pembayaran.qr', $order->id) }}" class="btn-bayar">
                                <i class="fas fa-wallet"></i> Bayar
                            </a>
                        @endif
                        <button class="btn-detail-icon" onclick="openNzModal({{ $order->id }})" title="Lihat Detail">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Modal Detail -->
                <div class="nz-modal" id="modalDetail{{ $order->id }}">
                    <div class="nz-modal-overlay" onclick="closeNzModal({{ $order->id }})"></div>
                    <div class="nz-modal-container">
                        <div class="nz-modal-header">
                            <h5 class="nz-modal-title">Rincian Pesanan #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</h5>
                            <button type="button" class="nz-modal-close" onclick="closeNzModal({{ $order->id }})">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="nz-modal-body">
                            <!-- Daftar Item -->
                            <div class="order-items-list">
                                @foreach($order->items as $item)
                                    <div class="item-row">
                                        <img src="{{ $item->product->image ? $item->product->image_url : asset('image/5.png') }}" alt="" class="item-img">
                                        <div class="item-info">
                                            <div class="item-name">{{ $item->product->name }}</div>
                                            <div class="item-price">{{ $item->quantity }} kg x Rp {{ number_format($item->price, 0, ',', '.') }}</div>
                                        </div>
                                        <div class="item-subtotal">Rp {{ number_format($item->quantity * $item->price, 0, ',', '.') }}</div>
                                    </div>
                                @endforeach
                            </div>

                            <hr style="border:none; border-top:1px dashed #eef2ee; margin: 20px 0;">

                            <!-- Rincian Pembayaran -->
                            <div class="payment-details">
                                <div class="payment-row">
                                    <span>Total Produk</span>
                                    <span>Rp {{ number_format($order->total_produk, 0, ',', '.') }}</span>
                                </div>
                                <div class="payment-row">
                                    <span>Ongkos Kirim</span>
                                    <span>Rp {{ number_format($order->ongkir, 0, ',', '.') }}</span>
                                </div>
                                <div class="payment-row grand">
                                    <span>Grand Total</span>
                                    <span>Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <hr style="border:none; border-top:1px dashed #eef2ee; margin: 20px 0;">

                            <!-- Info Lainnya -->
                            <div class="info-details">
                                <div>
                                    <i class="fas fa-money-check-alt" style="color:var(--sage); margin-top:4px; font-size:1.1rem;"></i>
                                    <div>
                                        <strong>Metode Pembayaran</strong>
                                        {{ strtoupper($order->metode_pembayaran) }}
                                    </div>
                                </div>
                                <div>
                                    <i class="fas fa-truck" style="color:var(--sage); margin-top:4px; font-size:1.1rem;"></i>
                                    <div>
                                        <strong>Pengiriman</strong>
                                        {{ $order->metode_pengiriman == 'pengantaran' ? 'Dikirim ke Alamat Tujuan' : 'Diambil Sendiri di Kebun' }}
                                    </div>
                                </div>
                                @if($order->metode_pengiriman == 'pengantaran')
                                <div>
                                    <i class="fas fa-map-marker-alt" style="color:var(--sage); margin-top:4px; font-size:1.1rem;"></i>
                                    <div>
                                        <strong>Alamat Lengkap</strong>
                                        {{ $order->alamat }}
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <!-- Pagination -->
    @php
        $showPagination = !request()->has('all') && ($orders->total() > 8);
    @endphp

    @if($showPagination)
    <div class="pagination-wrapper">
        <div id="viewAllPlaceholder" style="display:none;">
            <li class="page-item">
                <a href="{{ url()->current() }}?all=1" class="page-link btn-view-all">View All</a>
            </li>
        </div>
        {{ $orders->appends(request()->query())->links('pagination::bootstrap-4') }}
    </div>
    @endif
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

        if (viewAllLi && pagination) {
            viewAllLi.classList.add('view-all-item');
            const lastItem = pagination.lastElementChild;
            pagination.insertBefore(viewAllLi, lastItem);
        } else if (viewAllLi && !pagination) {
            const wrapper = document.querySelector('.pagination-wrapper');
            if (wrapper) {
                const ul = document.createElement('ul');
                ul.className = 'pagination';
                ul.appendChild(viewAllLi);
                wrapper.appendChild(ul);
            }
        }
    });
</script>

@endsection
