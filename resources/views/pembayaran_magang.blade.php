@extends('master')

@section('konten')
<link rel="stylesheet" href="{{ asset('css/pengguna/pembayaran_magang.css') }}">
<div class="payment-wrapper pembayaran-magang-page">
    <div class="payment-card-premium">
        <header class="payment-header">
            @if(isset($payment_method_active) && $payment_method_active == 'transfer')
                <h1>Pembayaran Transfer Bank</h1>
                <ul class="payment-guide instruction-steps" style="margin-top: 15px;">
                    <li><i class="fas fa-check-circle" style="color: var(--sage-green); margin-top: 2px;"></i> <span>Salin nomor rekening di bawah ini.</span></li>
                    <li><i class="fas fa-check-circle" style="color: var(--sage-green); margin-top: 2px;"></i> <span>Lakukan transfer dari bank mana saja atau dompet digital (Dana, OVO, dll).</span></li>
                    <li><i class="fas fa-check-circle" style="color: var(--sage-green); margin-top: 2px;"></i> <span>Simpan dan unggah bukti transfer pada form di sebelah kanan.</span></li>
                </ul>
            @else
                <h1>Pembayaran QRIS</h1>
                <ul class="payment-guide instruction-steps" style="margin-top: 15px;">
                    <li><i class="fas fa-check-circle" style="color: var(--sage-green); margin-top: 2px;"></i> <span>Pindai (scan) kode QR di bawah menggunakan aplikasi M-Banking atau e-Wallet.</span></li>
                    <li><i class="fas fa-check-circle" style="color: var(--sage-green); margin-top: 2px;"></i> <span>Pastikan nominal pembayaran sesuai dengan total tagihan.</span></li>
                    <li><i class="fas fa-check-circle" style="color: var(--sage-green); margin-top: 2px;"></i> <span>Simpan dan unggah tangkapan layar (screenshot) bukti pembayaran pada form di sebelah kanan.</span></li>
                </ul>
            @endif
            <div class="timer-badge">
                <i class="far fa-clock"></i> <span>Selesaikan dalam:</span> <span id="time-left">--:--</span>
            </div>
        </header>

        <div class="payment-flex-container" style="display: flex; gap: 40px; align-items: flex-start; flex-wrap: wrap;">
            {{-- Kolom Kiri: Pembayaran --}}
            <div class="payment-left-col">
                @if(isset($payment_method_active) && $payment_method_active == 'transfer')
                    <div class="bank-transfer-frame" style="text-align: center; padding: 20px; background: #f8f9fa; border-radius: 12px; border: 1px solid #dee2e6; height: 100%; display: flex; flex-direction: column; justify-content: center;">
                        <i class="fas fa-university" style="font-size: 3rem; color: #007bff; margin-bottom: 15px;"></i>
                        <h4 style="font-weight: bold; color: #343a40;">{{ $bank_name ?? 'Bank Transfer' }}</h4>
                        
                        <div id="rek-box" class="account-number-box" style="margin: 20px 0; padding: 15px; background: #fff; border: 2px dashed #007bff; border-radius: 8px; transition: all 0.3s ease;">
                            <span style="font-size: 0.9rem; color: #6c757d; display: block; margin-bottom: 5px;">Nomor Rekening:</span>
                            <strong id="rek-number" style="font-size: 1.5rem; color: #007bff; letter-spacing: 2px; transition: color 0.3s ease;">{{ $bank_account_number ?? '-' }}</strong>
                            <button type="button" id="btn-copy-rek" class="btn btn-sm" style="display: block; margin: 10px auto 0; background: #e7f1ff; color: #007bff; border: 1px solid #007bff; border-radius: 6px; padding: 5px 15px; transition: all 0.3s ease;" onclick="copyRekening()">
                                <i class="far fa-copy" id="icon-copy-rek"></i> <span id="text-copy-rek">Salin Nomor Rekening</span>
                            </button>
                        </div>
                        
                        <span style="font-size: 0.9rem; color: #6c757d; display: block; margin-bottom: 5px;">Atas Nama:</span>
                        <strong style="font-size: 1.1rem; color: #343a40;">{{ $bank_account_owner ?? '-' }}</strong>
                    </div>
                @else
                    <div class="qr-frame">
                        <img src="{{ $qrUrl ? $qrUrl : '' }}" id="qris-image" alt="QRIS Code">
                    </div>
                    @if($qrUrl)
                    <a href="{{ route('nazfram.pembayaran.unduh-qr') }}" class="btn-download-qris">
                        <i class="fas fa-download"></i> Unduh Kode QR
                    </a>
                    @endif
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
    function copyRekening() {
        var text = document.getElementById("rek-number").innerText;
        navigator.clipboard.writeText(text).then(function() {
            var box = document.getElementById("rek-box");
            var number = document.getElementById("rek-number");
            var btn = document.getElementById("btn-copy-rek");
            var btnText = document.getElementById("text-copy-rek");
            var icon = document.getElementById("icon-copy-rek");

            if (box) box.style.borderColor = '#6c757d';
            if (number) number.style.color = '#6c757d';
            
            if (btn) {
                btn.style.color = '#6c757d';
                btn.style.borderColor = '#6c757d';
                btn.style.background = '#f8f9fa';
            }
            
            if (btnText) btnText.innerText = 'Tersalin';
            if (icon) icon.className = 'fas fa-check';
        }).catch(function(err) {
            console.error('Gagal menyalin: ', err);
        });
    }

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
        fetch("{{ route('nazfram.pembayaran_magang.expire', $pendaftaran->id_pendaftaran) }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        }).finally(() => {
            Swal.fire({
                title: 'Waktu Habis!',
                text: 'Batas waktu pembayaran 25 menit telah berakhir. Pendaftaran dibatalkan.',
                icon: 'warning',
                confirmButtonText: 'Ke Riwayat'
            }).then(() => {
                window.location.href = "{{ route('magang.riwayat') }}";
            });
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


