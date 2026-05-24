@extends('master')

@section('konten')
<link rel="stylesheet" href="{{ asset('css/pengguna/pembayaran.css') }}">
<div class="payment-wrapper page-pembayaran-produk">
    <div class="payment-card-premium">
        <header class="payment-header">
            <h1>Pembayaran QRIS</h1>
            <p class="payment-guide">Scan kode QR dan unggah bukti pembayaran untuk konfirmasi.</p>
            <div class="timer-badge">
                <i class="far fa-clock"></i> <span>Selesaikan dalam:</span> <span id="time-left">--:--</span>
            </div>
        </header>

        <div class="payment-flex-container">
            {{-- Kolom Kiri: QR Code --}}
            <div class="payment-left-col">
                <div class="qr-frame">
                    <img src="{{ $qrUrl ? asset('storage/' . $qrUrl) : '' }}" id="qris-image" alt="QRIS Code">
                </div>
                @if($qrUrl)
                <a href="{{ asset('storage/' . $qrUrl) }}" download="QRIS-Nazfram.png" class="btn-download-qris">
                    <i class="fas fa-download"></i> Unduh Kode QR
                </a>
                @endif
            </div>

            {{-- Kolom Kanan: Detail & Form --}}
            <div class="payment-right-col">
                <div class="amount-box mb-4" style="text-align: left; background: #ebf5eb; border: 1px solid #c2e0c2;">
                    <span class="amount-label" style="color: #2d5a27; font-weight: 700;">Total yang harus dibayar:</span>
                    <div class="amount-value" style="color: #1b3a1a; font-size: 2rem;">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</div>
                </div>
                <div class="integrated-summary">
                    <h6 class="instructions-title" style="margin-top: 0; margin-bottom: 15px;">Ringkasan Pesanan:</h6>
                    <div class="summary-row">
                        <span>ID Pesanan</span>
                        <span>#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="summary-row">
                        <span>Metode</span>
                        <span>QRIS Manual</span>
                    </div>
                    <div class="summary-row">
                        <span>Status</span>
                        <span class="text-warning">Menunggu Pembayaran</span>
                    </div>
                </div>

                <div class="payment-instructions">
                    <span class="instructions-title">Unggah Bukti Pembayaran:</span>
                    <form id="formBuktiPembayaran" enctype="multipart/form-data">
                        @csrf
                        <div class="custom-file-upload mb-3">
                            <input type="file" name="bukti_pembayaran" class="form-control" accept="image/*" required 
                                style="font-size: 0.9rem; padding: 12px; height: auto; border-radius: 12px; border: 1px dashed #ced4da;">
                            <small class="text-muted d-block mt-2">Format: JPG, PNG (Max 10MB)</small>
                        </div>
                        <button type="submit" class="btn-pay-now">
                            <i class="fas fa-check-circle"></i> Konfirmasi Pembayaran
                        </button>
                    </form>
                </div>

                <div style="text-align: center;">
                    <button type="button" class="btn-cancel-pay" id="btn-batalkan-transaksi" style="width: 100%; margin-top: 15px;">
                        Batal & Kembali
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>

    // Form Upload Bukti Pembayaran
    document.getElementById('formBuktiPembayaran').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        Swal.fire({
            title: 'Mengirim...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        fetch("{{ route('nazfram.pembayaran.konfirmasi', $order->id) }}", {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Berhasil!', data.message, 'success').then(() => {
                    window.location.href = "{{ route('nazfram.riwayat-pesanan') }}";
                });
            } else {
                Swal.fire('Gagal', data.message || 'Terjadi kesalahan', 'error');
            }
        })
        .catch(err => {
            Swal.fire('Error', 'Terjadi kesalahan pada server', 'error');
        });
    });

    // Timer Logic
    const expiresAt = new Date("{{ \Carbon\Carbon::parse($order->expires_at)->toIso8601String() }}").getTime();
    const timerInterval = setInterval(updateTimer, 1000);

    function updateTimer() {
        const now = new Date().getTime();
        const distance = expiresAt - now;
        if (distance < 0) {
            clearInterval(timerInterval);
            handleExpiration();
            return;
        }
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        document.getElementById("time-left").innerHTML =
            (minutes < 10 ? "0" + minutes : minutes) + ":" +
            (seconds < 10 ? "0" + seconds : seconds);
    }

    function handleExpiration() {
        Swal.fire({
            title: 'Waktu Habis!',
            text: 'Batas waktu pembayaran telah berakhir.',
            icon: 'warning',
            confirmButtonText: 'Kembali ke Keranjang'
        }).then(() => {
            window.location.href = "{{ route('nazfram.keranjang') }}";
        });
    }

    // Batal Trigger
    document.getElementById('btn-batalkan-transaksi').onclick = function (e) {
        e.preventDefault();
        Swal.fire({
            title: 'Batalkan Pesanan?',
            text: "Produk akan dikembalikan ke keranjang Anda.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#1b3022',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Batalkan!',
            cancelButtonText: 'Tidak'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch("{{ route('nazfram.pesanan.expire', $order->id) }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = "{{ route('nazfram.keranjang') }}";
                    }
                });
            }
        });
    };
</script>
@endsection


