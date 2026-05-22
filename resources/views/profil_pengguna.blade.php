@extends('master')

@section('konten')
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- Global Feedback Handlers --}}
@if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Data Tidak Valid',
                html: '<div style="text-align:left;">@foreach ($errors->all() as $error)<div>- {{ $error }}</div>@endforeach</div>',
                confirmButtonColor: '#2d5a27'
            });
        });
    </script>
@endif

@if (session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: "{{ session('success') }}",
                timer: 2500,
                showConfirmButton: false
            });
        });
    </script>
@endif

<!-- Leaflet.js Assets -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

<!-- CSS Profil -->
<link rel="stylesheet" href="/css/styleprofil_pengguna.css">

<div class="profil-page-wrapper">
    <div class="profil-container">

        {{-- Page Header --}}
        <div class="profil-page-header">
            <h1>Profil Saya</h1>
            <p>Kelola data akun dan informasi pengiriman Anda</p>
        </div>

        {{-- Back Button - Dipindahkan ke bawah header --}}
        <div class="back-button-wrapper">
            <a href="{{ url()->previous() }}" class="btn-back">
                <i class="fas fa-arrow-left"></i> <span>Kembali ke Halaman Sebelumnya</span>
            </a>
        </div>

        {{-- CARD 1: DATA PRIBADI --}}
        <div class="profil-card">
            <div class="profil-card-header">
                <div class="icon-circle green"><i class="fas fa-id-badge"></i></div>
                <div class="profil-card-header-text">
                    <h3>Data Pribadi</h3>
                    <p>Informasi dasar dan titik lokasi rumah</p>
                </div>
            </div>
            <div class="profil-card-body">
                <form action="{{ route('nazfram.profil-saya.update') }}" method="POST">
                    @csrf
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="name">Nama Lengkap</label>
                            <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                        </div>
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" class="form-control" value="{{ $user->username }}" required>
                        </div>
                        <div class="form-group">
                            <label for="nohp">Nomor HP / WhatsApp</label>
                            <input type="text" id="nohp" name="nohp" class="form-control" value="{{ old('nohp', $user->nohp) }}" required>
                        </div>
                        <div class="form-group">
                            <label for="umur">Umur</label>
                            <input type="number" id="umur" name="umur" class="form-control" value="{{ old('umur', $user->umur) }}" required min="1">
                        </div>
                        <div class="form-group full">
                            <label for="alamat">Alamat Lengkap</label>
                            <textarea id="alamat" name="alamat" class="form-control" required>{{ old('alamat', $user->alamat) }}</textarea>
                        </div>

                        <div class="form-group full">
                            <label>Titik Lokasi Pengiriman (Maps)</label>
                            <div class="map-preview-box" id="openMapTrigger">
                                <div class="preview-overlay">
                                    <i class="fas fa-map-marked-alt"></i>
                                    <span>Klik untuk mengubah titik lokasi</span>
                                </div>
                                <div id="map-preview-small"></div>
                            </div>

                            <div class="coord-info">
                                <span>Lat: <strong id="latText">{{ $user->latitude ?? '-' }}</strong></span>
                                <span>Lng: <strong id="lngText">{{ $user->longitude ?? '-' }}</strong></span>
                            </div>

                            <input type="hidden" name="latitude" id="lat" value="{{ $user->latitude }}">
                            <input type="hidden" name="longitude" id="lng" value="{{ $user->longitude }}">
                        </div>
                    </div>

                    <div class="btn-save-row">
                        <button type="submit" class="btn-primary-green">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- CARD 2: EDIT EMAIL --}}
        <div class="profil-card">
            <div class="profil-card-header">
                <div class="icon-circle blue"><i class="fas fa-envelope"></i></div>
                <div class="profil-card-header-text">
                    <h3>Email Akun</h3>
                    <p>Ubah alamat email Anda dengan verifikasi</p>
                </div>
            </div>
            <div class="profil-card-body">
                <div class="form-group full" id="groupInitialEmail">
                    <label>Email Saat Ini</label>
                    <div class="email-input-group">
                        <input type="email" class="form-control" value="{{ $user->email }}" disabled>
                        <button type="button" class="btn-outline-action" id="btnEditEmail" onclick="kirimKode('email')">
                            <i class="fas fa-edit"></i> Edit Email
                        </button>
                    </div>
                </div>

                <div class="step-panel" id="panelOtpEmail">
                    <p class="step-title"><i class="fas fa-shield-alt"></i> Verifikasi Identitas</p>
                    <p class="otp-info">Silakan masukkan 6-digit kode verifikasi yang telah kami kirimkan ke email Anda.</p>
                    <div class="otp-input-group">
                        <div class="form-group">
                            <label>Kode OTP</label>
                            <input type="text" id="otpEmail" class="form-control otp-input" maxlength="6">
                        </div>
                        <button type="button" class="btn-primary-green" onclick="verifikasiKode('email')" id="btnVerifyEmail">Verifikasi</button>
                    </div>
                </div>

                <div class="step-panel" id="panelFormEmail">
                    <p class="step-title"><i class="fas fa-edit"></i> Email Baru <span class="verified-badge"><i class="fas fa-check"></i> Terverifikasi</span></p>
                    <div class="form-group">
                        <label>Masukkan Email Baru</label>
                        <input type="email" id="emailBaru" class="form-control">
                    </div>
                    <button type="button" class="btn-primary-green" onclick="saveNewEmail()">Update Email Baru</button>
                </div>
            </div>
        </div>

        {{-- CARD 3: EDIT SANDI --}}
        <div class="profil-card">
            <div class="profil-card-header">
                <div class="icon-circle amber"><i class="fas fa-key"></i></div>
                <div class="profil-card-header-text">
                    <h3>Keamanan Akun</h3>
                    <p>Ubah password Anda secara aman</p>
                </div>
            </div>
            <div class="profil-card-body">
                <div class="form-group full" id="groupInitialPassword">
                    <label>Password</label>
                    <div class="password-input-group">
                        <input type="text" class="form-control" value="••••••••••••" disabled>
                        <button type="button" class="btn-outline-action" id="btnEditPassword" onclick="kirimKode('password')">
                            <i class="fas fa-lock"></i> Edit Sandi
                        </button>
                    </div>
                </div>

                <div class="step-panel" id="panelOtpPassword">
                    <p class="step-title"><i class="fas fa-shield-alt"></i> Verifikasi Identitas</p>
                    <p class="otp-info">Silakan masukkan 6-digit kode verifikasi yang telah kami kirimkan ke email Anda untuk mengubah sandi.</p>
                    <div class="otp-input-group">
                        <div class="form-group">
                            <label>Kode OTP</label>
                            <input type="text" id="otpPassword" class="form-control otp-input" maxlength="6">
                        </div>
                        <button type="button" class="btn-primary-green" onclick="verifikasiKode('password')" id="btnVerifyPassword">Verifikasi</button>
                    </div>
                </div>

                <div class="step-panel" id="panelFormPassword">
                    <p class="step-title"><i class="fas fa-key"></i> Password Baru <span class="verified-badge"><i class="fas fa-check"></i> Terverifikasi</span></p>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Password Baru</label>
                            <div class="password-field-wrap">
                                <input type="password" id="passwordBaru" class="form-control">
                                <button type="button" class="toggle-pw" onclick="togglePw('passwordBaru',this)"><i class="fas fa-eye"></i></button>
                            </div>
                            <p class="password-hint"><i class="fas fa-info-circle"></i> Minimal 7 karakter, harus ada huruf besar, angka, dan simbol (!@#$%^&*).</p>
                        </div>
                        <div class="form-group">
                            <label>Konfirmasi Password</label>
                            <div class="password-field-wrap">
                                <input type="password" id="passwordBaruConfirm" class="form-control">
                                <button type="button" class="toggle-pw" onclick="togglePw('passwordBaruConfirm',this)"><i class="fas fa-eye"></i></button>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn-primary-green" onclick="saveNewPassword()">Update Password Baru</button>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- MODAL MAPS --}}
<div class="map-modal-overlay" id="mapModal">
    <div class="map-modal-content">
        <div class="map-modal-header">
            <h3>Tentukan Titik Lokasi Pengiriman</h3>
            <button class="btn-close-modal" id="closeMapBtn">&times;</button>
        </div>
        <div class="map-modal-body">
            <div id="map-full-container"></div>
        </div>
        <div class="map-modal-footer">
            <button class="btn-modal-action btn-modal-cancel" id="cancelMapBtn">Batal</button>
            <button class="btn-modal-action btn-modal-save" id="saveMapBtn">Simpan Lokasi</button>
        </div>
    </div>
</div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

/* ============================================
   MAPS LOGIC
============================================ */
let previewMap, mainMap, marker;
const initialLat = {{ $user->latitude ?? -6.3915 }};
const initialLng = {{ $user->longitude ?? 107.7553 }};
let tempLat = initialLat;
let tempLng = initialLng;

function initMaps() {
    previewMap = L.map('map-preview-small', { zoomControl: false, dragging: false, scrollWheelZoom: false, doubleClickZoom: false }).setView([initialLat, initialLng], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(previewMap);
    L.marker([initialLat, initialLng]).addTo(previewMap);

    const mapModal = document.getElementById('mapModal');
    const openMapTrigger = document.getElementById('openMapTrigger');

    openMapTrigger.addEventListener('click', () => {
        mapModal.classList.add('show');
        initMainMap();
    });

    document.getElementById('closeMapBtn').addEventListener('click', () => mapModal.classList.remove('show'));
    document.getElementById('cancelMapBtn').addEventListener('click', () => mapModal.classList.remove('show'));

    document.getElementById('saveMapBtn').addEventListener('click', () => {
        document.getElementById('lat').value = tempLat;
        document.getElementById('lng').value = tempLng;
        document.getElementById('latText').textContent = tempLat.toFixed(6);
        document.getElementById('lngText').textContent = tempLng.toFixed(6);

        previewMap.setView([tempLat, tempLng], 15);
        previewMap.eachLayer((layer) => { if (layer instanceof L.Marker) previewMap.removeLayer(layer); });
        L.marker([tempLat, tempLng]).addTo(previewMap);

        mapModal.classList.remove('show');
        Swal.fire({ icon: 'success', title: 'Lokasi Dipilih', text: 'Tekan tombol Simpan Perubahan untuk mengupdate.', timer: 2000, showConfirmButton: false });
    });
}

function initMainMap() {
    if (mainMap) {
        mainMap.invalidateSize();
        return;
    }
    mainMap = L.map('map-full-container').setView([tempLat, tempLng], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(mainMap);

    marker = L.marker([tempLat, tempLng], { draggable: true }).addTo(mainMap);

    marker.on('dragend', function(e) {
        const pos = e.target.getLatLng();
        tempLat = pos.lat; tempLng = pos.lng;
    });

    mainMap.on('click', function(e) {
        marker.setLatLng(e.latlng);
        tempLat = e.latlng.lat; tempLng = e.latlng.lng;
    });
}

document.addEventListener('DOMContentLoaded', initMaps);

/* ============================================
   OTP & EDIT FLOW
============================================ */
function kirimKode(jenis) {
    const btn = document.getElementById(jenis === 'email' ? 'btnEditEmail' : 'btnEditPassword');
    const panelOtp = document.getElementById(jenis === 'email' ? 'panelOtpEmail' : 'panelOtpPassword');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
    fetch('{{ route("nazfram.profil-saya.kirim-kode") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ jenis })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            panelOtp.classList.add('active-step');
            btn.innerHTML = '<i class="fas fa-check"></i> Kode Terkirim';
        } else {
            btn.disabled = false;
            btn.innerHTML = 'Edit ' + (jenis === 'email' ? 'Email' : 'Sandi');
            Swal.fire('Gagal', data.message, 'error');
        }
    });
}

function verifikasiKode(jenis) {
    const otp = document.getElementById(jenis === 'email' ? 'otpEmail' : 'otpPassword').value;
    const btn = document.getElementById(jenis === 'email' ? 'btnVerifyEmail' : 'btnVerifyPassword');
    const panelOtp = document.getElementById(jenis === 'email' ? 'panelOtpEmail' : 'panelOtpPassword');
    const panelForm = document.getElementById(jenis === 'email' ? 'panelFormEmail' : 'panelFormPassword');
    const groupInitial = document.getElementById(jenis === 'email' ? 'groupInitialEmail' : 'groupInitialPassword');

    if (otp.length < 6) return Swal.fire('Error', 'Input 6 digit kode OTP', 'warning');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    fetch('{{ route("nazfram.profil-saya.verifikasi-kode") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ kode: otp, jenis })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            Swal.fire({ icon: 'success', title: 'Verifikasi Berhasil', text: 'Silakan isi data baru Anda.', timer: 1500, showConfirmButton: false });
            groupInitial.style.display = 'none';
            panelOtp.style.display = 'none';
            panelForm.classList.add('active-step');
        } else {
            btn.disabled = false;
            btn.innerHTML = 'Verifikasi';
            Swal.fire('Error', data.message, 'error');
        }
    });
}

function saveNewEmail() {
    const email = document.getElementById('emailBaru').value;
    if (!email) return Swal.fire('Gagal', 'Email baru wajib diisi', 'warning');

    fetch('{{ route("nazfram.profil-saya.ubah-email") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ email_baru: email })
    }).then(async res => {
        const data = await res.json();
        if (res.ok) {
            Swal.fire('Berhasil', 'Email diperbarui', 'success').then(() => location.reload());
        } else {
            const msg = data.message || (data.errors ? Object.values(data.errors)[0][0] : 'Gagal memperbarui email');
            Swal.fire('Gagal', msg, 'error');
        }
    });
}

function saveNewPassword() {
    const pw = document.getElementById('passwordBaru').value;
    const pwc = document.getElementById('passwordBaruConfirm').value;
    if (!pw || pw.length < 7) return Swal.fire('Gagal', 'Password minimal 7 karakter', 'warning');
    if (pw !== pwc) return Swal.fire('Gagal', 'Konfirmasi password tidak cocok', 'warning');

    fetch('{{ route("nazfram.profil-saya.ubah-password") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ password_baru: pw, password_baru_confirmation: pwc })
    }).then(async res => {
        const data = await res.json();
        if (res.ok) {
            Swal.fire('Berhasil', 'Password diperbarui', 'success').then(() => location.reload());
        } else {
            const msg = data.message || (data.errors ? Object.values(data.errors)[0][0] : 'Gagal memperbarui password');
            Swal.fire('Gagal', msg, 'error');
        }
    });
}

function togglePw(id, btn) {
    const el = document.getElementById(id);
    const icon = btn.querySelector('i');
    if (el.type === 'password') { el.type = 'text'; icon.className = 'fas fa-eye-slash'; }
    else { el.type = 'password'; icon.className = 'fas fa-eye'; }
}
</script>
@endsection
