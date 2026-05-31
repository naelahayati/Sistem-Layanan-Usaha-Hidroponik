@extends('admin.Theme.defualt')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/settings.css') }}">
<style>
/* CSS for Interactive Payment Cards */
.payment-card-option {
    position: relative;
    display: flex;
    flex-direction: column;
    padding: 1.25rem;
    border-radius: 0.75rem;
    border: 2px solid #e5e7eb;
    cursor: pointer;
    background-color: #ffffff;
    transition: all 0.3s ease;
    user-select: none;
    height: 100%;
}
.payment-card-option:hover {
    border-color: #a7f3d0;
}
.payment-card-option input[type="radio"] {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border-width: 0;
}
.payment-card-option .icon-wrapper {
    padding: 0.5rem;
    border-radius: 0.5rem;
    background-color: #f3f4f6;
    color: #4b5563;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
}
.payment-card-option .indicator {
    width: 1.25rem;
    height: 1.25rem;
    border-radius: 9999px;
    border: 2px solid #d1d5db;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}
.payment-card-option .indicator-inner {
    width: 0.625rem;
    height: 0.625rem;
    border-radius: 9999px;
    background-color: transparent;
    transition: all 0.3s ease;
}

/* Checked State */
.payment-card-option.is-checked {
    border-color: #059669;
    background-color: #ecfdf5;
}
.payment-card-option.is-checked .icon-wrapper {
    background-color: #059669;
    color: #ffffff;
}
.payment-card-option.is-checked .indicator {
    border-color: #059669;
}
.payment-card-option.is-checked .indicator-inner {
    background-color: #059669;
}

/* Opacity transition for sections */
.payment-section {
    transition: opacity 0.3s ease;
}

/* Base styles replacing inline styles */
.payment-card-content { gap: 12px; }
.payment-card-icon { font-size: 1.2rem; }
.payment-card-title { font-size: 0.95rem; }
.payment-card-desc { font-size: 0.75rem; line-height: 1.2; }

/* Responsive adjustments for mobile */
@media (max-width: 768px) {
    .payment-card-option {
        padding: 0.75rem; /* ~p-3 */
    }
    .payment-card-content {
        gap: 0.75rem; /* ~gap-3 */
    }
    .payment-card-option .icon-wrapper {
        width: 32px; /* ~w-8 */
        height: 32px; /* ~h-8 */
    }
    .payment-card-icon {
        font-size: 1rem;
    }
    .payment-card-title {
        font-size: 0.875rem; /* ~text-sm */
    }
    .payment-card-desc {
        font-size: 11px; /* text-[11px] */
        line-height: 1.25; /* leading-tight */
    }
    .payment-card-option .indicator {
        width: 1rem;
        height: 1rem;
    }
    .payment-card-option .indicator-inner {
        width: 0.5rem;
        height: 0.5rem;
    }
}
</style>
@endpush

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold text-dark">Pengaturan Website</h1>
                <p class="text-muted small">Konfigurasi global untuk operasional Naz Hidrofarm.</p>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Pengaturan</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content page-settings">
    <div class="container-fluid">
        <div class="row">
            {{-- Form WhatsApp --}}
            <div class="col-12 mb-4">
                <div class="card shadow-sm border-0 h-100" style="border-radius: 15px;">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="font-weight-bold text-dark"><i class="fab fa-whatsapp mr-2 text-success"></i> Kontak WhatsApp</h5>
                        <p class="text-muted small mb-0">Nomor ini digunakan untuk konfirmasi pendaftaran PKL oleh pengguna.</p>
                    </div>
                    <div class="card-body p-4 d-flex flex-column">
                        <form class="form-settings-partial flex-grow-1">
                            @csrf
                            <div class="form-group mb-4">
                                <label class="small font-weight-bold text-muted text-uppercase mb-2">Nomor WhatsApp Admin</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-0" style="border-radius: 10px 0 0 10px;"><i class="fab fa-whatsapp text-success"></i></span>
                                    </div>
                                    <input type="text" name="whatsapp_admin" value="{{ $whatsapp }}" class="form-control border-0 bg-light px-3" placeholder="Contoh: 628123456789" style="border-radius: 0 10px 10px 0; height: 48px;" required>
                                </div>
                                <small class="text-muted mt-2 d-block">
                                    <i class="fas fa-info-circle mr-1"></i> Gunakan format internasional (awali dengan 62).
                                </small>
                            </div>
                            <div class="mt-auto">
                                <button type="submit" class="btn btn-success btn-block shadow-sm" style="border-radius: 12px; font-weight: bold; background: #28a745; border: none; height: 48px;">
                                    <i class="fas fa-save mr-2"></i> Simpan Nomor WA
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Form Pengaturan Pembayaran --}}
            <div class="col-12 mb-4">
                <div class="card shadow-sm border-0 h-100" style="border-radius: 15px;">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="font-weight-bold text-dark"><i class="fas fa-money-check-alt mr-2 text-primary"></i> Pengaturan Pembayaran Terpusat</h5>
                        <p class="text-muted small mb-0">Kontrol metode pembayaran untuk Produk, Kunjungan, dan Magang.</p>
                    </div>
                    <div class="card-body p-4 d-flex flex-column">
                        <form class="form-settings-partial flex-grow-1" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="form-group mb-4">
                                <label class="small font-weight-bold text-muted text-uppercase mb-3 d-block">Metode Pembayaran Aktif</label>
                                <div class="row">
                                    
                                    <!-- Pilihan 1: Via QR Code -->
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <label class="payment-card-option {{ (isset($payment_method_active) && $payment_method_active == 'qris') ? 'is-checked' : '' }}" id="card_method_qris">
                                            <input type="radio" id="method_qris" name="payment_method_active" value="qris" {{ (!isset($payment_method_active) || $payment_method_active == 'qris') ? 'checked' : '' }} onchange="updatePaymentCardSelection()">
                                            <div class="d-flex align-items-center justify-content-between h-100">
                                                <div class="d-flex align-items-center payment-card-content">
                                                    <div class="icon-wrapper">
                                                        <i class="fas fa-qrcode payment-card-icon"></i>
                                                    </div>
                                                    <div>
                                                        <span class="d-block font-weight-bold text-dark payment-card-title">Via QR Code</span>
                                                        <span class="d-block text-muted mt-1 payment-card-desc">Aktifkan pembayaran instan menggunakan QRIS</span>
                                                    </div>
                                                </div>
                                                <div class="indicator">
                                                    <div class="indicator-inner"></div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>

                                    <!-- Pilihan 2: Via Transfer Bank -->
                                    <div class="col-md-6">
                                        <label class="payment-card-option {{ (isset($payment_method_active) && $payment_method_active == 'transfer') ? 'is-checked' : '' }}" id="card_method_transfer">
                                            <input type="radio" id="method_transfer" name="payment_method_active" value="transfer" {{ (isset($payment_method_active) && $payment_method_active == 'transfer') ? 'checked' : '' }} onchange="updatePaymentCardSelection()">
                                            <div class="d-flex align-items-center justify-content-between h-100">
                                                <div class="d-flex align-items-center payment-card-content">
                                                    <div class="icon-wrapper">
                                                        <i class="fas fa-university payment-card-icon"></i>
                                                    </div>
                                                    <div>
                                                        <span class="d-block font-weight-bold text-dark payment-card-title">Via Transfer Bank</span>
                                                        <span class="d-block text-muted mt-1 payment-card-desc">Aktifkan pembayaran manual lewat Nomor Rekening</span>
                                                    </div>
                                                </div>
                                                <div class="indicator">
                                                    <div class="indicator-inner"></div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>

                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="row">
                                <div class="col-md-6 payment-section" id="section_qris" style="border-right: 1px solid #dee2e6; {{ (!isset($payment_method_active) || $payment_method_active == 'qris') ? '' : 'opacity: 0.5;' }}">
                                    <h6 class="font-weight-bold text-dark mb-3"><i class="fas fa-qrcode mr-2 text-primary"></i> Pengaturan QRIS</h6>
                                    <div class="form-group mb-4">
                                        @if(isset($qris_image) && $qris_image)
                                            <div class="mb-3 text-center">
                                                <img src="{{ $qris_image }}" alt="QRIS" class="img-thumbnail" style="max-width: 150px;">
                                            </div>
                                        @endif
                                        <label class="small font-weight-bold text-muted text-uppercase mb-2">Unggah Gambar QRIS Baru</label>
                                        <input type="file" name="qris_image" class="form-control" accept="image/*" style="height: auto; padding: 10px;">
                                        <small class="text-muted mt-2 d-block">Abaikan jika tidak ingin mengubah QR. Format: JPG, PNG, JPEG. Max: 10MB.</small>
                                    </div>
                                </div>
                                <div class="col-md-6 pl-4 payment-section" id="section_transfer" style="{{ (isset($payment_method_active) && $payment_method_active == 'transfer') ? '' : 'opacity: 0.5;' }}">
                                    <h6 class="font-weight-bold text-dark mb-3"><i class="fas fa-university mr-2 text-info"></i> Pengaturan Transfer Bank</h6>
                                    
                                    <div class="form-group mb-3">
                                        <label class="small font-weight-bold text-muted text-uppercase mb-2">Nama Bank</label>
                                        <input type="text" name="bank_name" value="{{ $bank_name ?? '' }}" class="form-control border-0 bg-light px-3" placeholder="Contoh: BCA / Mandiri" style="border-radius: 10px; height: 48px;">
                                    </div>
                                    
                                    <div class="form-group mb-3">
                                        <label class="small font-weight-bold text-muted text-uppercase mb-2">Nomor Rekening</label>
                                        <input type="text" name="bank_account_number" value="{{ $bank_account_number ?? '' }}" class="form-control border-0 bg-light px-3" placeholder="Contoh: 1234567890" style="border-radius: 10px; height: 48px;">
                                    </div>
                                    
                                    <div class="form-group mb-3">
                                        <label class="small font-weight-bold text-muted text-uppercase mb-2">Nama Pemilik Rekening</label>
                                        <input type="text" name="bank_account_owner" value="{{ $bank_account_owner ?? '' }}" class="form-control border-0 bg-light px-3" placeholder="Contoh: PT Naz Hidrofarm" style="border-radius: 10px; height: 48px;">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary btn-block shadow-sm" style="border-radius: 12px; font-weight: bold; background: #007bff; border: none; height: 48px;">
                                    <i class="fas fa-save mr-2"></i> Simpan Pengaturan Pembayaran
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.querySelectorAll('.form-settings-partial').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            Swal.fire({
                title: 'Menyimpan...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            fetch("{{ route('admin.settings.update') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Gagal', data.message, 'error');
                }
            })
            .catch(err => {
                Swal.fire('Gagal', 'Terjadi kesalahan pada server.', 'error');
            });
        });
    });

    function updatePaymentCardSelection() {
        const qrisRadio = document.getElementById('method_qris');
        const transferRadio = document.getElementById('method_transfer');
        
        const cardQris = document.getElementById('card_method_qris');
        const cardTransfer = document.getElementById('card_method_transfer');
        
        const sectionQris = document.getElementById('section_qris');
        const sectionTransfer = document.getElementById('section_transfer');

        if (qrisRadio && qrisRadio.checked) {
            cardQris.classList.add('is-checked');
            cardTransfer.classList.remove('is-checked');
            
            sectionQris.style.opacity = '1';
            sectionTransfer.style.opacity = '0.4';
            sectionTransfer.style.pointerEvents = 'none'; // Optional: disable clicking when inactive
            sectionQris.style.pointerEvents = 'auto';
        } else if (transferRadio && transferRadio.checked) {
            cardTransfer.classList.add('is-checked');
            cardQris.classList.remove('is-checked');
            
            sectionTransfer.style.opacity = '1';
            sectionQris.style.opacity = '0.4';
            sectionQris.style.pointerEvents = 'none';
            sectionTransfer.style.pointerEvents = 'auto';
        }
    }
    
    // Initial call to set correct visual state on load
    document.addEventListener('DOMContentLoaded', function() {
        updatePaymentCardSelection();
    });
</script>
@endsection
