@extends('admin.Theme.defualt')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/kelola_admin_form.css') }}">
@endpush

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold text-dark">{{ isset($admin) ? 'Edit Profil Admin' : 'Daftarkan Admin Baru' }}</h1>
                <p class="text-muted small">Kelola kredensial dan akses administratif Naz Hidrofarm.</p>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.kelola-admin') }}">Kelola Admin</a></li>
                    <li class="breadcrumb-item active">{{ isset($admin) ? 'Edit' : 'Tambah' }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content page-admin-form">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">


                @if(isset($admin))
                    {{-- ==================== MODE EDIT (FLOW SEPERTI PENGGUNA) ==================== --}}
                    
                    {{-- CARD 1: DATA PRIBADI --}}
                    <div class="card shadow-sm border-0 mb-4" style="border-radius: 15px;">
                        <div class="card-header bg-white border-0 pt-4 px-4">
                            <h5 class="font-weight-bold text-dark"><i class="fas fa-id-card mr-2 text-primary"></i> Data Pribadi</h5>
                            <p class="text-muted small mb-0">Ubah nama dan identitas admin.</p>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('admin.edit', $admin->id) }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="small font-weight-bold text-muted">NAMA LENGKAP</label>
                                        <input type="text" class="form-control border-0 bg-light px-3" name="name" value="{{ old('name', $admin->name) }}" style="border-radius: 10px; height: 45px;" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="small font-weight-bold text-muted">USERNAME</label>
                                        <input type="text" class="form-control border-0 bg-light px-3" name="username" value="{{ old('username', $admin->username) }}" style="border-radius: 10px; height: 45px;" required>
                                    </div>
                                    {{-- Hidden fields to pass existing email to main update --}}
                                    <input type="hidden" name="email" value="{{ $admin->email }}">
                                </div>
                                <div class="d-flex justify-content-end align-items-center mt-3">
                                    <a href="{{ route('admin.kelola-admin') }}" class="btn btn-light shadow-sm px-4 mr-3" style="border-radius: 10px; font-weight: bold; border: 1px solid #ced4da; color: #495057;">Batal</a>
                                    <button type="submit" class="btn text-white px-5 shadow-sm" style="border-radius: 10px; font-weight: bold; background: linear-gradient(135deg, #0b5ed7, #4299e1); border: none;">Simpan Profil</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- CARD 2: EDIT EMAIL --}}
                    <div class="card shadow-sm border-0 mb-4" style="border-radius: 15px;">
                        <div class="card-header bg-white border-0 pt-4 px-4">
                            <h5 class="font-weight-bold text-dark"><i class="fas fa-envelope mr-2 text-warning"></i> Email Akun</h5>
                            <p class="text-muted small mb-0">Ubah alamat email dengan verifikasi kode unik.</p>
                        </div>
                        <div class="card-body p-4">
                            <div id="emailDisplay">
                                <label class="small font-weight-bold text-muted">EMAIL SAAT INI</label>
                                <div class="d-flex align-items-center">
                                    <input type="text" class="form-control border-0 bg-light px-3 mr-3" value="{{ $admin->email }}" disabled style="border-radius: 10px; height: 45px;">
                                    <button type="button" class="btn px-3" style="border-radius: 10px; font-weight: bold; white-space: nowrap; background-color: #fffbeb; color: #d97706; border: 1px solid #fcd34d;" onclick="kirimKode('email')">
                                        <i class="fas fa-edit mr-1"></i> Edit Email
                                    </button>
                                </div>
                            </div>

                            <div id="panelOtpEmail" class="d-none mt-3 p-3 border border-warning" style="border-radius: 12px; background: #fffdf5;">
                                <label class="small font-weight-bold text-warning mb-2"><i class="fas fa-shield-alt mr-1"></i> VERIFIKASI IDENTITAS</label>
                                <p class="small text-muted mb-3">Masukkan 6-digit kode verifikasi yang dikirim ke email <strong>{{ $admin->email }}</strong></p>
                                <div class="d-flex align-items-center">
                                    <input type="text" id="otpEmail" class="form-control border-0 bg-white shadow-sm mr-3 text-center" maxlength="6" style="border-radius: 10px; height: 45px; width: 150px; letter-spacing: 5px; font-weight: bold;">
                                    <button type="button" class="btn text-white px-4 shadow-sm" style="border-radius: 10px; font-weight: bold; background: linear-gradient(135deg, #f59e0b, #fbbf24); border: none;" onclick="verifikasiKode('email')">Verifikasi</button>
                                </div>
                            </div>

                            <div id="panelFormEmail" class="d-none mt-3">
                                <label class="small font-weight-bold text-muted">MASUKKAN EMAIL BARU</label>
                                <div class="d-flex align-items-center">
                                    <input type="email" id="emailBaru" class="form-control border-0 bg-white shadow-sm px-3 mr-3" style="border-radius: 10px; height: 45px; border: 1px solid #ffc107 !important;">
                                    <button type="button" class="btn text-white px-4 shadow-sm" style="border-radius: 10px; font-weight: bold; white-space: nowrap; background: linear-gradient(135deg, #1f2937, #4b5563); border: none;" onclick="updateEmail()">Update Email</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- CARD 3: EDIT PASSWORD --}}
                    <div class="card shadow-sm border-0 mb-4" style="border-radius: 15px;">
                        <div class="card-header bg-white border-0 pt-4 px-4">
                            <h5 class="font-weight-bold text-dark"><i class="fas fa-key mr-2 text-danger"></i> Keamanan Akun</h5>
                            <p class="text-muted small mb-0">Ubah password admin melalui verifikasi aman.</p>
                        </div>
                        <div class="card-body p-4">
                            <div id="passwordDisplay">
                                <label class="small font-weight-bold text-muted">PASSWORD</label>
                                <div class="d-flex align-items-center">
                                    <input type="text" class="form-control border-0 bg-light px-3 mr-3" value="••••••••••••" disabled style="border-radius: 10px; height: 45px;">
                                    <button type="button" class="btn px-3" style="border-radius: 10px; font-weight: bold; white-space: nowrap; background-color: #fef2f2; color: #dc2626; border: 1px solid #fecaca;" onclick="kirimKode('password')">
                                        <i class="fas fa-lock mr-1"></i> Edit Sandi
                                    </button>
                                </div>
                            </div>

                            <div id="panelOtpPassword" class="d-none mt-3 p-3 border border-danger" style="border-radius: 12px; background: #fff5f5;">
                                <label class="small font-weight-bold text-danger mb-2"><i class="fas fa-shield-alt mr-1"></i> VERIFIKASI IDENTITAS</label>
                                <p class="small text-muted mb-3">Masukkan 6-digit kode verifikasi yang dikirim ke email <strong>{{ $admin->email }}</strong></p>
                                <div class="d-flex align-items-center">
                                    <input type="text" id="otpPassword" class="form-control border-0 bg-white shadow-sm mr-3 text-center" maxlength="6" style="border-radius: 10px; height: 45px; width: 150px; letter-spacing: 5px; font-weight: bold;">
                                    <button type="button" class="btn text-white px-4 shadow-sm" style="border-radius: 10px; font-weight: bold; background: linear-gradient(135deg, #dc2626, #f87171); border: none;" onclick="verifikasiKode('password')">Verifikasi</button>
                                </div>
                            </div>

                            <div id="panelFormPassword" class="d-none mt-3">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="small font-weight-bold text-muted">PASSWORD BARU</label>
                                        <div class="position-relative">
                                            <input type="password" id="passwordBaru" class="form-control border-0 bg-white shadow-sm px-3" style="border-radius: 10px; height: 45px; border: 1px solid #dc3545 !important;">
                                            <button type="button" onclick="togglePw('passwordBaru', this)" class="position-absolute" style="right:12px; top:50%; transform:translateY(-50%); background:none; border:none; color:#aaa;"><i class="fas fa-eye"></i></button>
                                        </div>
                                        <small class="text-muted"><i class="fas fa-info-circle mr-1"></i> Minimal 7 karakter, harus ada huruf besar, angka, dan simbol (!@#$%^&*).</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="small font-weight-bold text-muted">KONFIRMASI PASSWORD</label>
                                        <div class="position-relative">
                                            <input type="password" id="passwordConfirm" class="form-control border-0 bg-white shadow-sm px-3" style="border-radius: 10px; height: 45px; border: 1px solid #dc3545 !important;">
                                            <button type="button" onclick="togglePw('passwordConfirm', this)" class="position-absolute" style="right:12px; top:50%; transform:translateY(-50%); background:none; border:none; color:#aaa;"><i class="fas fa-eye"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <button type="button" class="btn text-white px-4 shadow-sm" style="border-radius: 10px; font-weight: bold; background: linear-gradient(135deg, #1f2937, #4b5563); border: none;" onclick="updatePassword()">Update Password Baru</button>
                                </div>
                            </div>
                        </div>
                    </div>

                @else
                    {{-- ==================== MODE TAMBAH (SINGLE FORM) ==================== --}}
                    <div class="card shadow-sm border-0" style="border-radius: 15px;">
                        <div class="card-body p-5">
                            <form action="{{ route('admin.add') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label class="small font-weight-bold text-muted text-uppercase mb-2"><i class="fas fa-user-circle mr-1"></i> Nama Lengkap</label>
                                        <input type="text" class="form-control border-0 bg-light px-3" name="name" value="{{ old('name') }}" style="border-radius: 10px; height: 48px;" required>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="small font-weight-bold text-muted text-uppercase mb-2"><i class="fas fa-at mr-1"></i> Username</label>
                                        <input type="text" class="form-control border-0 bg-light px-3" name="username" value="{{ old('username') }}" style="border-radius: 10px; height: 48px;" required>
                                    </div>
                                    <div class="col-md-12 mb-4">
                                        <label class="small font-weight-bold text-muted text-uppercase mb-2"><i class="fas fa-envelope mr-1"></i> Alamat Email</label>
                                        <input type="email" class="form-control border-0 bg-light px-3" name="email" value="{{ old('email') }}" style="border-radius: 10px; height: 48px;" required>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="small font-weight-bold text-muted text-uppercase mb-2"><i class="fas fa-lock mr-1"></i> Password</label>
                                        <div class="position-relative">
                                            <input type="password" id="pwAdd" class="form-control border-0 bg-light px-3" name="password" style="border-radius: 10px; height: 48px;" required>
                                            <button type="button" onclick="togglePw('pwAdd', this)" style="position:absolute; right:12px; top:50%; transform:translateY(-50%); background:none; border:none; color:#aaa;"><i class="fas fa-eye"></i></button>
                                        </div>
                                        <small class="text-muted"><i class="fas fa-info-circle mr-1"></i> Minimal 7 karakter, harus ada huruf besar, angka, dan simbol (!@#$%^&*).</small>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="small font-weight-bold text-muted text-uppercase mb-2"><i class="fas fa-check-double mr-1"></i> Konfirmasi Password</label>
                                        <div class="position-relative">
                                            <input type="password" id="pwAddC" class="form-control border-0 bg-light px-3" name="password_confirmation" style="border-radius: 10px; height: 48px;" required>
                                            <button type="button" onclick="togglePw('pwAddC', this)" style="position:absolute; right:12px; top:50%; transform:translateY(-50%); background:none; border:none; color:#aaa;"><i class="fas fa-eye"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4 d-flex justify-content-end align-items-center">
                                    <a href="{{ route('admin.kelola-admin') }}" class="btn btn-light shadow-sm px-4 py-2 mr-3" style="border-radius: 12px; font-weight: bold; border: 1px solid #ced4da; font-size: 1.1rem; color: #495057;">Batal</a>
                                    <button type="submit" class="btn text-white shadow-sm px-5 py-2" style="border-radius: 12px; font-weight: bold; background: linear-gradient(135deg, #0b5ed7, #4299e1); border: none; font-size: 1.1rem;">
                                        <i class="fas fa-save mr-2"></i> Simpan Admin
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif



            </div>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const adminId = "{{ $admin->id ?? '' }}";
    const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function kirimKode(jenis) {
        Swal.fire({ title: 'Mengirim Kode...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
        
        fetch("{{ route('admin.admin.kirim-kode') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ id: adminId, jenis: jenis })
        })
        .then(res => res.json())
        .then(data => {
            Swal.close();
            if (data.success) {
                if(jenis === 'email') {
                    document.getElementById('panelOtpEmail').classList.remove('d-none');
                } else {
                    document.getElementById('panelOtpPassword').classList.remove('d-none');
                }
                Swal.fire('Berhasil', data.message, 'success');
            } else {
                Swal.fire('Gagal', data.message, 'error');
            }
        });
    }

    function verifikasiKode(jenis) {
        const otp = document.getElementById(jenis === 'email' ? 'otpEmail' : 'otpPassword').value;
        if (otp.length < 6) return Swal.fire('Peringatan', 'Masukkan 6 digit kode.', 'warning');

        fetch("{{ route('admin.admin.verifikasi-kode') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ id: adminId, kode: otp })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Berhasil', 'Verifikasi sukses! Silakan masukkan data baru.', 'success');
                if(jenis === 'email') {
                    document.getElementById('panelOtpEmail').classList.add('d-none');
                    document.getElementById('panelFormEmail').classList.remove('d-none');
                    document.getElementById('emailDisplay').classList.add('d-none');
                } else {
                    document.getElementById('panelOtpPassword').classList.add('d-none');
                    document.getElementById('panelFormPassword').classList.remove('d-none');
                    document.getElementById('passwordDisplay').classList.add('d-none');
                }
            } else {
                Swal.fire('Gagal', data.message, 'error');
            }
        });
    }

    function updateEmail() {
        const email = document.getElementById('emailBaru').value;
        if(!email) return Swal.fire('Gagal', 'Email baru wajib diisi', 'warning');

        fetch("{{ route('admin.admin.ubah-email') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ id: adminId, email_baru: email })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Berhasil', data.message, 'success').then(() => location.reload());
            } else {
                Swal.fire('Gagal', data.message, 'error');
            }
        });
    }

    function updatePassword() {
        const pw = document.getElementById('passwordBaru').value;
        const pwc = document.getElementById('passwordConfirm').value;
        if(!pw || pw.length < 7) return Swal.fire('Gagal', 'Password minimal 7 karakter', 'warning');
        if(pw !== pwc) return Swal.fire('Gagal', 'Konfirmasi password tidak cocok', 'warning');

        fetch("{{ route('admin.admin.ubah-password') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ id: adminId, password_baru: pw, password_baru_confirmation: pwc })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Berhasil', data.message, 'success').then(() => location.reload());
            } else {
                Swal.fire('Gagal', data.message, 'error');
            }
        });
    }

    function togglePw(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'fas fa-eye-slash';
        } else {
            input.type = 'password';
            icon.className = 'fas fa-eye';
        }
    }
</script>

@endsection
