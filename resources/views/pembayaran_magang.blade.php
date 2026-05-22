@extends('master')

@section('konten')
<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap');

    :root {
        --primary-green: #1b3a1a;
        --sage-green: #87a96b;
        --soft-sage: #f8faf8;
        --accent-yellow: #f2b50b;
        --text-dark: #1b3a1a;
        --text-muted: #64748b;
        --glass-bg: rgba(255, 255, 255, 0.98);
    }

    .payment-wrapper {
        min-height: calc(100vh - 100px);
        background: #f4f7f4;
        display: flex;
        justify-content: center;
        align-items: flex-start;
        padding: 120px 20px 40px;
        font-family: 'DM Sans', sans-serif;
    }

    .payment-card-premium {
        background: var(--glass-bg);
        width: 100%;
        max-width: 850px;
        border-radius: 24px;
        padding: 40px;
        box-shadow: 0 25px 50px rgba(27, 58, 26, 0.12);
        border: 1px solid rgba(135, 169, 107, 0.1);
        position: relative;
        animation: cardAppear 0.6s ease-out;
    }

    @keyframes cardAppear {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .payment-flex-container {
        display: flex;
        gap: 40px;
        align-items: flex-start;
        flex-wrap: wrap;
    }

    .payment-left-col {
        flex: 1;
        min-width: 300px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .payment-right-col {
        flex: 1.2;
        min-width: 300px;
    }

    .payment-header {
        text-align: left;
        margin-bottom: 30px;
        width: 100%;
    }

    .payment-header h1 {
        font-size: 1.8rem;
        font-weight: 800;
        color: var(--primary-green);
        margin-bottom: 8px;
    }

    .payment-guide {
        font-size: 0.95rem;
        color: var(--text-muted);
    }

    .timer-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #fff8e1;
        color: #b48608;
        padding: 8px 16px;
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: 700;
        margin-top: 15px;
    }

    .qr-frame {
        background: #fff;
        padding: 20px;
        border-radius: 24px;
        border: 2px solid #f1f5f1;
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        margin-bottom: 20px;
        width: 100%;
        max-width: 320px;
    }

    .qr-frame img {
        width: 100%;
        height: auto;
        display: block;
        border-radius: 12px;
    }

    .amount-box {
        text-align: center;
        width: 100%;
        background: #f8faf8;
        padding: 20px;
        border-radius: 20px;
        border: 1px solid #eef2ee;
    }

    .amount-label {
        font-size: 0.85rem;
        color: var(--text-muted);
        display: block;
        margin-bottom: 6px;
    }

    .amount-value {
        font-size: 1.8rem;
        font-weight: 800;
        color: var(--primary-green);
    }

    .integrated-summary {
        margin-bottom: 24px;
        background: #fff;
        border: 1px solid #f1f5f1;
        border-radius: 20px;
        padding: 20px;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 12px;
        font-size: 0.9rem;
    }

    .summary-row:last-child {
        margin-bottom: 0;
    }

    .summary-row span:first-child {
        color: var(--text-muted);
    }

    .summary-row span:last-child {
        font-weight: 700;
        color: var(--text-dark);
    }

    .btn-pay-now {
        width: 100%;
        background: var(--primary-green);
        color: #fff;
        border: none;
        padding: 18px;
        border-radius: 16px;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 8px 20px rgba(27, 58, 26, 0.2);
    }

    .btn-pay-now:hover {
        background: #234722;
        transform: translateY(-2px);
        box-shadow: 0 12px 25px rgba(27, 58, 26, 0.3);
    }

    .payment-instructions {
        margin-top: 0;
        background: #f8faf8;
        padding: 20px;
        border-radius: 20px;
        border: 1px solid #eef2ee;
    }

    .btn-download-qris {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        max-width: 320px;
        padding: 14px;
        background: #f0f7f0;
        color: #2d5a27;
        border: 1px solid #c2e0c2;
        border-radius: 16px;
        font-size: 0.9rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 12px rgba(45, 90, 39, 0.05);
    }

    .btn-download-qris:hover {
        background: #2d5a27;
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(45, 90, 39, 0.15);
        text-decoration: none;
    }

    .btn-download-qris i {
        font-size: 1.1rem;
    }
        margin-bottom: 10px;
        display: block;
    }

    .instruction-steps {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .instruction-steps li {
        font-size: 0.75rem;
        margin-bottom: 8px;
        display: flex;
        gap: 8px;
        line-height: 1.4;
    }

    .btn-cancel-pay {
        display: inline-block;
        background: #f1f5f1;
        border: 1px solid #e2e8f0;
        color: #64748b;
        font-size: 0.85rem;
        font-weight: 500;
        cursor: pointer;
        margin-top: 20px;
        text-decoration: none;
        padding: 10px 20px;
        border-radius: 10px;
        transition: all 0.2s;
    }

    .btn-cancel-pay:hover {
        background: #fee2e2;
        color: #ef4444;
        border-color: #fecaca;
    }
</style>

<div class="payment-wrapper">
    <div class="payment-card-premium">
        <header class="payment-header">
            <h1>Pembayaran QRIS</h1>
            <p class="payment-guide">Scan kode QR dan unggah bukti pembayaran untuk konfirmasi.</p>
            <div class="timer-badge">
                <i class="far fa-clock"></i> <span>Selesaikan dalam:</span> <span id="time-left">--:--</span>
            </div>
        </header>

        <div class="payment-flex-container" style="display: flex; gap: 40px; align-items: flex-start; flex-wrap: wrap;">
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
                    <div class="amount-value" style="color: #1b3a1a; font-size: 2rem;">Rp {{ number_format($pendaftaran->total_harga, 0, ',', '.') }}</div>
                </div>
                <div class="integrated-summary">
                    <h6 class="instructions-title" style="margin-top: 0; margin-bottom: 15px;">Detail Pendaftaran:</h6>
                    <div class="summary-row">
                        <span>ID Pendaftaran</span>
                        <span>#{{ str_pad($pendaftaran->id_pendaftaran, 5, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="summary-row">
                        <span>Paket</span>
                        <span>{{ $pendaftaran->paket_name }}</span>
                    </div>
                    <div class="summary-row">
                        <span>Durasi</span>
                        <span>{{ $pendaftaran->durasi_magang }} Bulan</span>
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

        fetch("{{ route('nazfram.pembayaran_magang.konfirmasi', $pendaftaran->id_pendaftaran) }}", {
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
                    window.location.href = "{{ route('magang.riwayat') }}?payment_success=true";
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
    const expiresAt = new Date("{{ \Carbon\Carbon::parse($pendaftaran->expires_at)->toIso8601String() }}").getTime();
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
            confirmButtonText: 'Kembali'
        }).then(() => {
            window.location.href = "{{ route('nazfram.pelatihan') }}";
        });
    }

    // Batal Trigger
    document.getElementById('btn-batalkan-transaksi').onclick = function (e) {
        e.preventDefault();
        Swal.fire({
            title: 'Batalkan Pembayaran?',
            text: "Data pendaftaran Anda tidak akan tersimpan jika Anda kembali sekarang.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Batalkan',
            cancelButtonText: 'Tidak, Lanjut Bayar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch("{{ route('nazfram.pembayaran_magang.batal', $pendaftaran->id_pendaftaran) }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Dibatalkan', 'Transaksi pendaftaran Anda telah dibatalkan.', 'success')
                        .then(() => window.location.href = "{{ route('nazfram.pelatihan') }}");
                    }
                });
            }
        });
    };
</script>
@endsection
