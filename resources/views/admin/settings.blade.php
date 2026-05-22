@extends('admin.Theme.defualt')

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

<section class="content">
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

            {{-- Form QRIS --}}
            <div class="col-12 mb-4">
                <div class="card shadow-sm border-0 h-100" style="border-radius: 15px;">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="font-weight-bold text-dark"><i class="fas fa-qrcode mr-2 text-primary"></i> QRIS Pembayaran</h5>
                        <p class="text-muted small mb-0">Unggah gambar QRIS yang akan ditampilkan kepada pengguna saat melakukan pembayaran.</p>
                    </div>
                    <div class="card-body p-4 d-flex flex-column">
                        <form class="form-settings-partial flex-grow-1" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group mb-4">
                                @if($qris_image)
                                    <div class="mb-3 text-center">
                                        <img src="{{ asset('storage/' . $qris_image) }}" alt="QRIS" class="img-thumbnail" style="max-width: 150px;">
                                    </div>
                                @endif
                                <label class="small font-weight-bold text-muted text-uppercase mb-2">Unggah Gambar QRIS Baru</label>
                                <input type="file" name="qris_image" class="form-control" accept="image/*" style="height: auto; padding: 10px;" required>
                                <small class="text-muted mt-2 d-block">Format: JPG, PNG, JPEG. Max: 10MB.</small>
                            </div>
                            <div class="mt-auto">
                                <button type="submit" class="btn btn-primary btn-block shadow-sm" style="border-radius: 12px; font-weight: bold; background: #007bff; border: none; height: 48px;">
                                    <i class="fas fa-upload mr-2"></i> Update Gambar QRIS
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
</script>
@endsection
