@extends('master')

@section('konten')
<link rel="stylesheet" href="{{ asset('css/pengguna/riwayat_pendaftaran.css') }}">
<link rel="stylesheet" href="/css/styleriwayat.css">
<main class="produk-page">
    <!-- Hero Header -->
    <header class="magang-hero page-header-sub">
        <div class="hero-content">
            <h1>RIWAYAT PENDAFTARAN</h1>
        </div>
    </header>

    <!-- Tombol Kembali -->
    <div class="action-bar-riwayat">
        <a href="{{ route($backRoute) }}" class="btn-back-riwayat">
            <i class="fas fa-chevron-left"></i>
            <span>Kembali ke Magang</span>
        </a>
    </div>

    @php
        $adminPhone = \App\Models\Setting::get('whatsapp_admin', '6282240867746');
        $adminPhone = preg_replace('/[^0-9]/', '', $adminPhone);
        if (str_starts_with($adminPhone, '0')) {
            $adminPhone = '62' . substr($adminPhone, 1);
        }
    @endphp

    <!-- Grid Kartu Pesanan -->
    <div class="container-riwayat">
        @if($data->isEmpty())
            <div class="riwayat-empty">
                <i class="fas fa-user-graduate"></i>
                <p>Belum ada riwayat pendaftaran magang yang ditemukan.</p>
                <a href="{{ route($backRoute) }}" class="btn-back-riwayat" style="margin-top:20px;">
                    Daftar Sekarang
                </a>
            </div>
        @else
            @foreach($data as $item)
                <div class="order-card">

                    <!-- Header Card -->
                    <div class="order-header">
                        <div class="order-header-left">
                            <span class="order-id">Pendaftaran #REG-{{ str_pad($item->id_pendaftaran, 5, '0', STR_PAD_LEFT) }}</span>
                            <span class="order-date">{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y, H:i') }}</span>
                            @if(auth()->user()->role === 'admin')
                                <div class="user-info-badge" style="margin-top: 5px; font-weight: 800; color: #1b3a1a; font-size: 0.9rem;">
                                    <i class="fas fa-user-circle mr-1"></i> User: {{ $item->user_name }}
                                </div>
                            @endif
                        </div>
                        <span class="order-status-badge
                            @php
                                $status = strtoupper($item->status_pembayaran);
                                $isPending = in_array($status, ['PENDING', 'MENUNGGU PEMBAYARAN', 'MENUNGGU KONFIRMASI', 'TERKONFIRMASI']);
                                $isSuccess = in_array($status, ['LUNAS', 'DITERIMA', 'SELESAI', 'BERHASIL']);
                            @endphp
                            {{ $isPending ? 'status-pending' : ($isSuccess ? 'status-completed' : 'status-cancelled') }}">
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
                            <i class="fas fa-calendar-alt"></i>
                            Mulai: {{ \Carbon\Carbon::parse($item->tanggal_magang)->format('d M Y') }} ({{ $item->durasi_magang }} Bulan)
                        </div>
                    </div>

                    <!-- Aksi Card -->
                    <div class="order-actions" style="gap: 12px;">
                        {{-- Tombol Bayar: Muncul saat status Terkonfirmasi atau Menunggu Pembayaran (QRIS) --}}
                        @php
                            $canBayarTerkonfirmasi = $item->status_pembayaran == 'Terkonfirmasi' && $item->total_harga > 0;
                            $canBayarQris = $item->status_pembayaran == 'Menunggu Pembayaran'
                                && $item->metode_pembayaran == 'qris'
                                && $item->expires_at
                                && \Carbon\Carbon::parse($item->expires_at)->isFuture();
                        @endphp
                        @if($canBayarTerkonfirmasi || $canBayarQris)
                            <a href="{{ route('nazfram.pembayaran_magang', $item->id_pendaftaran) }}" class="btn-bayar" data-id="{{ $item->id_pendaftaran }}">
                                <i class="fas fa-wallet"></i> Bayar
                            </a>
                        @endif

                        @if($item->status_pembayaran == 'Menunggu Konfirmasi' && $item->is_wa_confirmation == 1 && $item->metode_pembayaran != 'qris')
                            @php
                                $pesanWA = "Assalamu'alaikum Warahmatullahi Wabarakatuh,\n\n" .
                                "Halo Admin Naz Hidrofarm,\n\n" .
                                "Perkenalkan, saya:\n\n" .
                                "Nama        : " . auth()->user()->name . "\n" .
                                "ID Daftar   : #MAG-" . str_pad($item->id_pendaftaran, 4, '0', STR_PAD_LEFT) . "\n" .
                                "Paket       : " . ($item->paket_name ?? 'Magang') . "\n" .
                                "Tgl Mulai   : " . \Carbon\Carbon::parse($item->tanggal_magang)->format('d M Y') . "\n" .
                                "Durasi      : " . $item->durasi_magang . " Bulan\n\n" .
                                "Saya ingin mengonfirmasi pendaftaran saya di Naz Hidrofarm dan menyatakan kesiapan untuk mengikuti program sesuai jadwal yang telah ditentukan.\n\n" .
                                "Mohon kiranya pendaftaran saya dapat segera diproses. Atas perhatian dan kerjasamanya, saya ucapkan terima kasih.\n\n" .
                                "Wassalamu'alaikum Warahmatullahi Wabarakatuh.\n\n" .
                                "Hormat saya,\n" .
                                auth()->user()->name;
                                $waLink = "https://wa.me/" . $adminPhone . "?text=" . urlencode($pesanWA);
                            @endphp

                            <a href="{{ $waLink }}" target="_blank" class="btn-wa">
                                <span>Konfirmasi WA</span>
                            </a>
                        @endif

                        <button class="btn-detail-icon" onclick="openNzModal({{ $item->id_pendaftaran }})" title="Lihat Detail">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Modal Detail -->
                <div class="nz-modal" id="modalDetail{{ $item->id_pendaftaran }}">
                    <div class="nz-modal-overlay" onclick="closeNzModal({{ $item->id_pendaftaran }})"></div>
                    <div class="nz-modal-container">
                        <div class="nz-modal-header">
                            <h5 class="nz-modal-title">Rincian Pendaftaran #REG-{{ str_pad($item->id_pendaftaran, 5, '0', STR_PAD_LEFT) }}</h5>
                            <button type="button" class="nz-modal-close" onclick="closeNzModal({{ $item->id_pendaftaran }})">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="nz-modal-body">
                            <!-- Daftar Item -->
                            <div class="order-items-list">
                                <div class="item-row">
                                    <div style="width:50px;height:50px;background:#e8f0e7;color:#2d5a27;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;">
                                        <i class="fas fa-user-tie"></i>
                                    </div>
                                    <div class="item-info" style="margin-left: 15px;">
                                        <div class="item-name">{{ $item->paket_name ?? 'Magang / PKL' }}</div>
                                        <div class="item-price">{{ $item->durasi_magang }} Bulan</div>
                                    </div>
                                    <div class="item-subtotal">Rp {{ number_format($item->total_harga, 0, ',', '.') }}</div>
                                </div>
                            </div>

                            <hr style="border:none; border-top:1px dashed #eef2ee; margin: 20px 0;">

                            <!-- Rincian Pembayaran -->
                            <div class="payment-details">
                                <div class="payment-row grand">
                                    <span>Total Biaya Magang</span>
                                    <span>Rp {{ number_format($item->total_harga, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <hr style="border:none; border-top:1px dashed #eef2ee; margin: 20px 0;">

                            <!-- Info Lainnya -->
                            <div class="info-details">
                                <div>
                                    <i class="fas fa-building" style="color:var(--sage); margin-top:4px; font-size:1.1rem;"></i>
                                    <div>
                                        <strong>Instansi / Pekerjaan</strong>
                                        {{ $item->pekerjaan }}
                                    </div>
                                </div>
                                <div>
                                    <i class="fas fa-calendar-check" style="color:var(--sage); margin-top:4px; font-size:1.1rem;"></i>
                                    <div>
                                        <strong>Periode Magang</strong>
                                        {{ \Carbon\Carbon::parse($item->tanggal_magang)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($item->tanggal_magang)->addMonths($item->durasi_magang)->format('d M Y') }}
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
                                        <strong>Status Pembayaran</strong>
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

    // Otomatis Buka WA (untuk pendaftaran yang butuh konfirmasi)
   @if(session('wa_link'))
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Pendaftaran Berhasil!',
                html: 'Pendaftaran Anda telah kami terima.<br><br>Silakan klik tombol di bawah untuk konfirmasi via WhatsApp ke admin.',
                icon: 'success',
                showCancelButton: true,
                confirmButtonText: '<i class="fab fa-whatsapp"></i> Buka WhatsApp',
                cancelButtonText: 'Nanti Saja',
                confirmButtonColor: '#25d366',
                cancelButtonColor: '#6c757d',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.open("{{ session('wa_link') }}", "_blank");
                }
            });
        }
    });
    @endif

    // Perbarui status pembayaran tanpa refresh halaman (polling ringan)
    document.querySelectorAll('.btn-bayar[data-id]').forEach(function(btn) {
        const id = btn.getAttribute('data-id');
        const card = btn.closest('.order-card');
        const badge = card ? card.querySelector('.order-status-badge') : null;

        setInterval(function() {
            fetch("{{ url('pelatihan/pembayaran/status') }}/" + id, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                if (!data.success) return;
                if (badge && data.status) {
                    badge.textContent = data.status;
                    if (data.status === 'Expired' || data.status === 'Dibatalkan') {
                        badge.classList.remove('status-pending');
                        badge.classList.add('status-cancelled');
                        btn.remove();
                    }
                }
                if (!data.can_pay && data.status === 'Menunggu Pembayaran' && btn) {
                    btn.remove();
                }
            })
            .catch(() => {});
        }, 15000);
    });
</script>

@endsection


