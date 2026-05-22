@extends('master')

@section('konten')
<link rel="stylesheet" href="/css/stylecheckout_magang.css">

<div class="checkout-wrapper">
    <div class="checkout-card">
        <div class="checkout-header">
            <h1>Pilih Metode Pembayaran</h1>
            <p>Pendaftaran Anda telah terkonfirmasi. Silakan pilih metode pembayaran untuk melanjutkan.</p>
        </div>

        <div class="transaction-details">
            <div class="detail-row">
                <span class="detail-label">ID Pendaftaran</span>
                <span class="detail-value">#REG-{{ str_pad($pendaftaran->id_pendaftaran, 5, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Paket Program</span>
                <span class="detail-value">{{ $pendaftaran->paket_name }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Tanggal Mulai</span>
                <span class="detail-value">{{ \Carbon\Carbon::parse($pendaftaran->tanggal_magang)->format('d M Y') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Durasi</span>
                <span class="detail-value">{{ $pendaftaran->durasi_magang }} Bulan</span>
            </div>
            <div class="detail-row total-row">
                <span class="detail-label">Total Pembayaran</span>
                <span class="detail-value">Rp {{ number_format($pendaftaran->total_harga, 0, ',', '.') }}</span>
            </div>
        </div>

        <form action="{{ route('nazfram.checkout_magang.proses', $pendaftaran->id_pendaftaran) }}" method="POST">
            @csrf
            <div class="payment-options">
                <h3>Metode Pembayaran</h3>

                <label class="radio-card active" id="card-qris">
                    <input type="radio" name="metode_pembayaran" id="radio-qris" value="qris" checked>
                    <div class="option-info">
                        <strong>QRIS</strong>
                        <span>Bayar instan via e-Wallet atau M-Banking</span>
                    </div>
                </label>
            </div>

            <button type="submit" class="btn-checkout-confirm">
                Konfirmasi Pembayaran
            </button>
        </form>

        <div class="back-link">
            <a href="{{ route('magang.riwayat') }}">
                <i class="fas fa-arrow-left"></i> Kembali ke Riwayat
            </a>
        </div>
    </div>
</div>

<script>
    function toggleActive(el) {
        document.querySelectorAll('.radio-card').forEach(card => card.classList.remove('active'));
        el.parentElement.classList.add('active');
    }
</script>
@endsection
