@extends('master')

@section('konten')
<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<div class="registration-page-wrapper">
    <div class="registration-card-modern">
        <!-- Logo & Header Section -->
        <div class="registration-header-modern">
            <div class="logo-container-modern">
                <img src="{{ asset('image/logo.png') }}" alt="Logo" class="img-logo-reg-modern">
            </div>
            <div class="header-titles">
                <h1 class="reg-title">Pendaftaran Akun</h1>
                <p class="reg-subtitle">Bergabunglah dengan komunitas Hidroponik Naz Hidrofarm</p>
            </div>
        </div>

        <div class="registration-body-modern">
            <form action="{{ route('do.register') }}" method="POST" id="regForm" autocomplete="off">
                @csrf
                <div class="modern-form-grid">
                    <div class="input-modern-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: John Doe" required class="@error('name') is-invalid @enderror">
                        @error('name')
                            <small class="error-text"><i class="fas fa-exclamation-circle"></i> {{ $message }}</small>
                        @enderror
                    </div>

                    <div class="input-modern-group">
                        <label>Username</label>
                        <input type="text" name="username" value="{{ old('username') }}" placeholder="Username unik anda" required class="@error('username') is-invalid @enderror">
                        @error('username')
                            <small class="error-text"><i class="fas fa-exclamation-circle"></i> {{ $message }}</small>
                        @enderror
                    </div>

                    <div class="input-modern-group">
                        <label>Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required class="@error('email') is-invalid @enderror">
                        @error('email')
                            <small class="error-text"><i class="fas fa-exclamation-circle"></i> {{ $message }}</small>
                        @enderror
                    </div>

                    <div class="input-modern-group">
                        <label>Password</label>
                        <div class="pw-wrapper-modern">
                            <input type="password" name="password" id="password" placeholder="Minimal 7 karakter" required class="@error('password') is-invalid @enderror">
                            <i class="fas fa-eye" id="togglePassword"></i>
                        </div>
                        <small class="password-hint"><i class="fas fa-info-circle"></i> Minimal 7 karakter, harus ada huruf besar, angka, dan simbol (!@#$%^&*).</small>
                        @error('password')
                            <small class="error-text"><i class="fas fa-exclamation-circle"></i> {{ $message }}</small>
                        @enderror
                    </div>

                    <div class="input-modern-group">
                        <label>Nomor HP (WhatsApp)</label>
                        <input type="text" name="nohp" value="{{ old('nohp') }}" placeholder="08XXXXXXXXXX" required class="@error('nohp') is-invalid @enderror">
                        @error('nohp')
                            <small class="error-text"><i class="fas fa-exclamation-circle"></i> {{ $message }}</small>
                        @enderror
                    </div>

                    <div class="input-modern-group">
                        <label>Umur</label>
                        <input type="number" name="umur" value="{{ old('umur') }}" placeholder="Contoh: 25" required class="@error('umur') is-invalid @enderror">
                        @error('umur')
                            <small class="error-text"><i class="fas fa-exclamation-circle"></i> {{ $message }}</small>
                        @enderror
                    </div>

                    <div class="input-modern-group full-width">
                        <label>Alamat Lengkap</label>
                        <textarea name="alamat" rows="3" placeholder="Tuliskan alamat lengkap pengiriman lokasi anda secara detail..." required class="@error('alamat') is-invalid @enderror">{{ old('alamat') }}</textarea>
                        @error('alamat')
                            <small class="error-text"><i class="fas fa-exclamation-circle"></i> {{ $message }}</small>
                        @enderror
                    </div>

                    <div class="input-modern-group full-width">
                        <label class="map-label">
                            <i class="fas fa-map-marker-alt"></i> Lokasi Pengiriman (Pin Map)
                        </label>
                        <div class="map-preview-box" id="openMapTrigger">
                            <div class="preview-overlay">
                                <i class="fas fa-map-pin"></i>
                                <span>Klik untuk pilih lokasi</span>
                            </div>
                            <div id="map-preview-small"></div>
                        </div>
                        <div class="map-status-badge" id="mapStatusText">
                            <i class="fas fa-circle-info"></i> Lokasi Belum Ditentukan
                        </div>
                        <input type="hidden" name="latitude" id="lat" value="{{ old('latitude') }}">
                        <input type="hidden" name="longitude" id="lng" value="{{ old('longitude') }}">
                    </div>

                    <div class="btn-container-modern">
                        <button type="submit" class="btn-submit-modern">Daftar Sekarang</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="registration-footer-modern">
            <p>Sudah memiliki akun? <a href="{{ route('login') }}">Masuk Sekarang</a></p>
            <a href="{{ route('nazfram.home') }}" class="back-link">
                <i class="fas fa-arrow-left"></i> Kembali ke Beranda
            </a>
        </div>
    </div>
</div>

<!-- MAP MODAL STRUCTURE -->
<div class="map-modal-overlay" id="mapModal">
    <div class="map-modal-content">
        <div class="map-modal-header">
            <div class="header-left">
                <i class="fas fa-map-marked-alt"></i>
                <h3>Tentukan Lokasi Rumah</h3>
            </div>
            <button class="btn-close-modal" id="closeMapBtn">&times;</button>
        </div>
        <div class="map-modal-body">
            <div id="map-full-container"></div>
            <div class="map-footer-hint">
                <i class="fas fa-info-circle"></i> Geser pin atau klik pada peta untuk menentukan lokasi yang tepat.
            </div>
        </div>
        <div class="map-modal-footer">
            <button type="button" class="btn-cancel-map" id="cancelMapBtn">Batal</button>
            <button type="button" class="btn-save-location" id="saveLocationBtn">Simpan Titik Lokasi</button>
        </div>
    </div>
</div>

<style>
    :root {
        --color-sage-green: #b2c9ab;
        --color-forest-green: #1b3a1a;
        --color-deep-green: #142d13;
        --color-cream: #fdfcf0;
        --color-accent-yellow: #fbbf24;
        --glass-white: rgba(255, 255, 255, 0.85);
        --glass-border: rgba(255, 255, 255, 0.4);
    }

    * {
        -webkit-tap-highlight-color: transparent;
        box-sizing: border-box;
    }

    /* Desktop Styles */
    .registration-page-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        width: 100%;
        background: linear-gradient(135deg, var(--color-sage-green) 0%, var(--color-cream) 100%);
        padding: 60px 20px;
    }

    .registration-card-modern {
        background: var(--glass-white);
        backdrop-filter: blur(15px);
        border: 1px solid var(--glass-border);
        border-radius: 32px;
        width: 100%;
        max-width: 900px;
        padding: 35px 45px;
        box-shadow: 0 30px 60px rgba(0,0,0,0.1);
    }

    .registration-header-modern {
        text-align: center;
        margin-bottom: 30px;
    }

    .img-logo-reg-modern {
        width: 70px;
        height: auto;
        margin-bottom: 12px;
    }

    .reg-title {
        color: var(--color-forest-green);
        margin: 0;
        font-size: 26px;
        font-weight: 800;
    }

    .reg-subtitle {
        color: #666;
        font-size: 14px;
        margin-top: 6px;
    }

    .modern-form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px 30px;
    }

    .input-modern-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .input-modern-group.full-width {
        grid-column: span 2;
    }

    .input-modern-group label {
        font-size: 14px;
        font-weight: 700;
        color: var(--color-forest-green);
        padding-left: 5px;
    }

    .input-modern-group input,
    .input-modern-group textarea {
        padding: 14px 18px;
        background: #fff;
        border: 1.5px solid #e5e7eb;
        border-radius: 16px;
        font-size: 14px;
        color: #333;
        outline: none;
        width: 100%;
    }

    .input-modern-group input:focus,
    .input-modern-group textarea:focus {
        border-color: var(--color-forest-green);
        box-shadow: 0 0 0 4px rgba(27, 58, 26, 0.1);
    }

    .pw-wrapper-modern {
        position: relative;
        width: 100%;
    }

    .pw-wrapper-modern input {
        width: 100%;
        padding-right: 45px;
    }

    .pw-wrapper-modern i {
        position: absolute;
        right: 18px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #999;
        font-size: 16px;
    }

    .password-hint {
        font-size: 11px;
        color: #666;
        margin-top: 4px;
        display: block;
    }

    .error-text {
        color: #ef4444;
        margin-top: 4px;
        font-size: 11px;
        display: block;
    }

    /* Map Preview */
    .map-label {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .map-preview-box {
        position: relative;
        height: 140px;
        width: 100%;
        border-radius: 20px;
        border: 2px dashed #cbd5e1;
        background: #f8fafc;
        overflow: hidden;
        cursor: pointer;
    }

    #map-preview-small {
        height: 100%;
        width: 100%;
    }

    .preview-overlay {
        position: absolute;
        z-index: 10;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        background: rgba(255, 255, 255, 0.5);
        color: var(--color-forest-green);
        font-weight: 700;
        gap: 8px;
        pointer-events: none;
    }

    .preview-overlay i {
        font-size: 28px;
    }

    .preview-overlay span {
        font-size: 13px;
    }

    .map-status-badge {
        margin-top: 10px;
        font-size: 12px;
        font-weight: 600;
        color: #ef4444;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .map-status-badge.set {
        color: var(--color-forest-green);
    }

    /* Map Modal */
    .map-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(8px);
        z-index: 10000;
        display: none;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }

    .map-modal-overlay.show {
        display: flex;
    }

    .map-modal-content {
        background: #fff;
        width: 100%;
        max-width: 900px;
        height: 85vh;
        border-radius: 32px;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .map-modal-header {
        padding: 20px 30px;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .header-left h3 {
        margin: 0;
        color: var(--color-forest-green);
        font-size: 18px;
        font-weight: 800;
    }

    .btn-close-modal {
        border: none;
        background: none;
        font-size: 32px;
        color: #9ca3af;
        cursor: pointer;
    }

    .map-modal-body {
        flex: 1;
        position: relative;
    }

    #map-full-container {
        height: 100%;
        width: 100%;
    }

    .map-footer-hint {
        position: absolute;
        bottom: 20px;
        left: 20px;
        right: 20px;
        background: rgba(255, 255, 255, 0.95);
        padding: 10px 18px;
        border-radius: 14px;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .map-modal-footer {
        padding: 18px 30px;
        background: #fafafa;
        display: flex;
        justify-content: flex-end;
        gap: 15px;
        border-top: 1px solid #f0f0f0;
    }

    .btn-cancel-map {
        padding: 12px 28px;
        background: #e5e7eb;
        border: none;
        border-radius: 14px;
        font-weight: 700;
        cursor: pointer;
    }

    .btn-save-location {
        padding: 12px 32px;
        background: var(--color-forest-green);
        color: #fff;
        border: none;
        border-radius: 14px;
        font-weight: 700;
        cursor: pointer;
    }

    /* ========== BUTTON CENTER ========== */
    .btn-container-modern {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 25px;
        width: 100%;
        grid-column: span 2;
    }

    .btn-submit-modern {
        width: auto;
        min-width: 260px;
        padding: 14px 40px;
        background: var(--color-forest-green);
        color: #fff;
        border: none;
        border-radius: 50px;
        font-size: 16px;
        font-weight: 800;
        cursor: pointer;
        text-align: center;
        display: inline-flex;
        justify-content: center;
        align-items: center;
    }

    .btn-submit-modern:hover {
        background: #3d7a35;
        transform: translateY(-3px);
    }

    .registration-footer-modern {
        text-align: center;
        margin-top: 28px;
        padding-top: 18px;
        border-top: 1px solid rgba(0,0,0,0.08);
        font-size: 14px;
        color: #777;
    }

    .registration-footer-modern p a {
        color: var(--color-accent-yellow);
        text-decoration: none;
        font-weight: 800;
    }

    .back-link {
        display: inline-block;
        margin-top: 12px;
        color: #999;
        font-weight: 600;
        text-decoration: none;
        font-size: 13px;
    }

    /* ========== RESPONSIVE ========== */
    @media screen and (max-width: 1024px) {
        .registration-card-modern {
            max-width: 780px;
            padding: 30px 35px;
        }
        .img-logo-reg-modern { width: 60px; }
        .reg-title { font-size: 22px; }
        .btn-submit-modern { min-width: 240px; padding: 13px 35px; font-size: 15px; }
    }

    @media screen and (max-width: 768px) {
        .registration-page-wrapper { padding: 40px 20px; }
        .registration-card-modern {
            max-width: 580px;
            padding: 25px 28px;
            border-radius: 28px;
        }
        .img-logo-reg-modern { width: 55px; }
        .reg-title { font-size: 20px; }
        .modern-form-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }
        .input-modern-group.full-width { grid-column: span 1; }
        .btn-container-modern { grid-column: span 1; }
        .btn-submit-modern { min-width: 200px; padding: 12px 30px; font-size: 14px; }
        .map-preview-box { height: 120px; }
    }

    @media screen and (max-width: 480px) {
        .registration-page-wrapper { padding: 20px 12px; }
        .registration-card-modern { padding: 16px 14px; border-radius: 20px; }
        .img-logo-reg-modern { width: 42px; }
        .reg-title { font-size: 18px; }
        .reg-subtitle { font-size: 10px; }
        .modern-form-grid { gap: 10px; }
        .input-modern-group label { font-size: 11px; }
        .input-modern-group input,
        .input-modern-group textarea {
            padding: 8px 12px;
            font-size: 13px;
            border-radius: 10px;
        }
        .map-preview-box { height: 85px; }
        .btn-submit-modern { min-width: 160px; padding: 10px 20px; font-size: 13px; }
        .map-modal-content { height: 80vh; }
        input, textarea, select { font-size: 13px !important; }
    }

    @media screen and (max-width: 360px) {
        .registration-card-modern { padding: 14px 12px; }
        .img-logo-reg-modern { width: 38px; }
        .reg-title { font-size: 16px; }
        .input-modern-group input,
        .input-modern-group textarea {
            padding: 7px 10px;
            font-size: 12px;
        }
        .btn-submit-modern { min-width: 140px; padding: 8px 16px; font-size: 12px; }
        input, textarea, select { font-size: 12px !important; }
    }

    @media screen and (max-width: 850px) and (orientation: landscape) {
        .registration-page-wrapper { padding: 15px 12px; }
        .registration-card-modern { padding: 12px 20px; }
        .modern-form-grid { grid-template-columns: repeat(2, 1fr); gap: 8px; }
        .input-modern-group.full-width { grid-column: span 2; }
        .map-preview-box { height: 70px; }
        .btn-submit-modern { padding: 8px 20px; font-size: 12px; }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle Password
        const togglePassword = document.querySelector('#togglePassword');
        const passwordField = document.querySelector('#password');
        if (togglePassword) {
            togglePassword.addEventListener('click', function() {
                const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordField.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        }

        // Map Logic
        const mapModal = document.getElementById('mapModal');
        const openMapBtn = document.getElementById('openMapTrigger');
        const closeMapBtn = document.getElementById('closeMapBtn');
        const cancelMapBtn = document.getElementById('cancelMapBtn');
        const saveLocationBtn = document.getElementById('saveLocationBtn');
        const mapStatusText = document.getElementById('mapStatusText');

        const farmLat = -6.391446647423662;
        const farmLng = 107.75575532459308;

        let tempLat = document.getElementById('lat').value || farmLat;
        let tempLng = document.getElementById('lng').value || farmLng;
        let previewMap = null;
        let mainMap = null;
        let userMarker = null;

        function initPreviewMap() {
            if (previewMap) previewMap.remove();
            previewMap = L.map('map-preview-small', {
                zoomControl: false,
                dragging: false,
                scrollWheelZoom: false,
                doubleClickZoom: false,
                touchZoom: false
            }).setView([tempLat, tempLng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(previewMap);
            L.marker([tempLat, tempLng]).addTo(previewMap);
        }
        initPreviewMap();

        function initMainMap() {
            if (mainMap) mainMap.remove();
            mainMap = L.map('map-full-container').setView([tempLat, tempLng], 16);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(mainMap);
            userMarker = L.marker([tempLat, tempLng], { draggable: true }).addTo(mainMap);
            userMarker.on('dragend', function(e) {
                tempLat = e.target.getLatLng().lat;
                tempLng = e.target.getLatLng().lng;
            });
            mainMap.on('click', function(e) {
                userMarker.setLatLng(e.latlng);
                tempLat = e.latlng.lat;
                tempLng = e.latlng.lng;
            });
        }

        openMapBtn.addEventListener('click', () => {
            mapModal.classList.add('show');
            setTimeout(() => {
                initMainMap();
                if (mainMap) {
                    mainMap.invalidateSize();
                    userMarker.setLatLng([tempLat, tempLng]);
                    mainMap.setView([tempLat, tempLng], 16);
                }
            }, 100);
        });

        const closeModal = () => mapModal.classList.remove('show');
        closeMapBtn.addEventListener('click', closeModal);
        cancelMapBtn.addEventListener('click', closeModal);
        window.addEventListener('click', (e) => { if (e.target === mapModal) closeModal(); });

        saveLocationBtn.addEventListener('click', () => {
            if (tempLat && tempLng) {
                document.getElementById('lat').value = tempLat;
                document.getElementById('lng').value = tempLng;
                mapStatusText.innerHTML = '<i class="fas fa-check-circle"></i> Lokasi Berhasil Disimpan';
                mapStatusText.classList.add('set');
                initPreviewMap();
                closeModal();
            }
        });

        if (document.getElementById('lat').value && document.getElementById('lng').value) {
            mapStatusText.innerHTML = '<i class="fas fa-check-circle"></i> Lokasi Berhasil Disimpan';
            mapStatusText.classList.add('set');
        }
    });
</script>
@endsection
