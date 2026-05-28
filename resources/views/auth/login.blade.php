@extends('master')

@section('konten')

<div class="auth-page-container">
    <div class="login-card-modern">
        <!-- Logo Section di Atas Form -->
        <div class="card-header-modern">
            <div class="logo-wrapper-modern">
                <img src="{{ asset('storage/image/logo.png') }}" alt="Logo" class="img-logo-modern">
            </div>
            <div class="brand-name-modern">
                <h2 class="brand-naz">Naz</h2>
                <h2 class="brand-hidro">HIDROFARM</h2>
            </div>
        </div>

        <div class="form-body-modern">
            <h3 class="form-title">Selamat Datang</h3>
            <p class="form-subtitle">Silakan masuk ke akun Anda</p>

            @if ($errors->has('lockout'))
                <div class="error-lockout-box">
                    <div class="lockout-icon"><i class="fas fa-lock"></i></div>
                    <div class="lockout-text">
                        <strong>Terlalu Banyak Percobaan Login</strong>
                        <p>{{ $errors->first('lockout') }}</p>
                    </div>
                </div>
            @elseif ($errors->any())
                <div class="error-simple-text">
                    {{ $errors->first('username') }}
                </div>
            @endif

            <form action="{{ route('do.login') }}" method="POST" autocomplete="off">
                @csrf
                <!-- Hidden inputs to prevent browser autocomplete issues -->
                <input type="text" style="display:none" aria-hidden="true">
                <input type="password" style="display:none" aria-hidden="true">

                <div class="input-container-modern">
                    <div class="input-group-modern">
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" name="username" placeholder="Username atau Email" value="{{ old('username') }}" oninvalid="this.setCustomValidity('Form ini harus diisi')" oninput="this.setCustomValidity('')" required>
                    </div>

                    <div class="input-group-modern">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" name="password" id="password" placeholder="Password" oninvalid="this.setCustomValidity('Form ini harus diisi')" oninput="this.setCustomValidity('')" required>
                        <i class="fas fa-eye toggle-password" id="togglePassword"></i>
                    </div>
                </div>

                <div class="login-utilities">
                    <label class="remember-checkbox">
                        <input type="checkbox" name="remember">
                        <span class="checkmark"></span>
                        Ingat Saya
                    </label>
                    <a href="javascript:void(0)" class="forgot-link" id="btnLupaPassword">Lupa Password?</a>
                </div>

                <button type="submit" class="btn-login-modern">Login</button>
            </form>

            <div class="form-footer-modern">
                <p>Belum punya akun? <a href="{{ route('register') }}">Buat Akun</a></p>
                <a href="{{ route('nazfram.home') }}" class="back-link">
                    <i class="fas fa-arrow-left"></i> Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    :root {
        --color-sage-green: #b2c9ab;
        --color-forest-green: #1b3a1a;
        --color-deep-green: #142d13;
        --color-cream: #fdfcf0;
        --color-water-blue: #0ea5e9;
        --color-bright-yellow: #fbbf24;
        --glass-bg: rgba(255, 255, 255, 0.75);
        --glass-border: rgba(255, 255, 255, 0.4);
        --font-main: 'Outfit', 'Inter', sans-serif;
    }

    .auth-page-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        width: 100%;
        background: linear-gradient(135deg, var(--color-sage-green) 0%, var(--color-cream) 100%) !important;
        padding: 40px 20px;
        box-sizing: border-box;
    }

    .login-card-modern {
        background: var(--glass-bg);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border: 1px solid var(--glass-border);
        border-radius: 28px;
        width: 100%;
        max-width: 400px;
        padding: 30px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
        text-align: center;
        animation: fadeInScale 0.7s cubic-bezier(0.165, 0.84, 0.44, 1);
    }

    @keyframes fadeInScale {
        from { transform: scale(0.96); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }

    .card-header-modern {
        margin-bottom: 20px;
    }

    .logo-wrapper-modern {
        width: 100%;
        margin: 0 auto 10px;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .img-logo-modern {
        width: 65px;
        height: auto;
        filter: drop-shadow(0 4px 6px rgba(0,0,0,0.05));
    }

    .brand-name-modern .brand-naz {
        color: var(--color-forest-green);
        margin: 0;
        font-size: 22px;
        font-weight: 800;
        letter-spacing: -0.5px;
    }

    .brand-name-modern .brand-hidro {
        color: var(--color-forest-green);
        margin: -4px 0 0;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 2px;
    }

    .form-title {
        color: var(--color-deep-green);
        margin: 0;
        font-size: 20px;
        font-weight: 700;
    }

    .form-subtitle {
        color: #666;
        font-size: 13px;
        margin: 5px 0 20px;
    }

    .input-container-modern {
        margin-bottom: 15px;
    }

    .input-group-modern {
        position: relative;
        margin-bottom: 12px;
    }

    .input-group-modern .input-icon {
        position: absolute;
        left: 18px;
        top: 50%;
        transform: translateY(-50%);
        color: #999;
        font-size: 14px;
    }

    .input-group-modern input {
        width: 100%;
        padding: 16px 50px;
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid rgba(0,0,0,0.05);
        border-radius: 16px;
        font-size: 14px;
        outline: none;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-sizing: border-box;
    }

    .input-group-modern input:focus {
        border-color: var(--color-forest-green);
        background: #fff;
        box-shadow: 0 0 0 4px rgba(27, 58, 26, 0.08);
    }

    .toggle-password {
        position: absolute;
        right: 18px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #888;
        padding: 5px;
        transition: color 0.3s;
    }
    .toggle-password:hover { color: var(--color-forest-green); }

    .login-utilities {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding: 0 5px;
    }

    .remember-checkbox {
        display: flex;
        align-items: center;
        font-size: 13px;
        color: #666;
        cursor: pointer;
        user-select: none;
    }

    .remember-checkbox input {
        margin-right: 10px;
        accent-color: var(--color-bright-yellow);
        width: 17px;
        height: 17px;
    }

    .forgot-link {
        color: var(--color-bright-yellow);
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        transition: 0.3s;
    }
    .forgot-link:hover { color: var(--color-forest-green); text-decoration: underline; }

    .btn-login-modern {
        width: 100%;
        padding: 16px;
        background: var(--color-forest-green);
        color: #fff;
        border: none;
        border-radius: 50px;
        font-size: 16px;
        font-weight: 800;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 8px 15px rgba(27, 58, 26, 0.2);
    }
    .btn-login-modern:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 25px rgba(27, 58, 26, 0.3);
        background: #3d7a35;
    }

    .form-footer-modern {
        margin-top: 25px;
        font-size: 13px;
        color: #777;
    }
    .form-footer-modern a { color: var(--color-bright-yellow); text-decoration: none; font-weight: 800; transition: 0.3s; }
    .form-footer-modern a:hover { color: var(--color-forest-green); text-decoration: underline; }

    .back-link {
        display: inline-block;
        margin-top: 15px;
        color: #999 !important;
        font-weight: 600 !important;
        font-size: 13px;
        transition: 0.3s ease;
    }
    .back-link:hover { color: var(--color-forest-green) !important; transform: translateX(-5px); }

    .error-simple-text {
        color: #b91c1c;
        margin-bottom: 20px;
        font-size: 13px;
        font-weight: normal;
    }

    /* Modal Styling Premium */
    .modal-custom {
        display: none;
        position: fixed;
        z-index: 3000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.35);
        backdrop-filter: blur(10px);
        align-items: center;
        justify-content: center;
        padding: 20px;
        transition: opacity 0.3s ease;
    }

    .modal-content-custom {
        background: var(--glass-bg);
        padding: 50px 40px;
        border-radius: 32px;
        width: 100%;
        max-width: 440px;
        box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.15);
        border: 1px solid var(--glass-border);
        position: relative;
        text-align: center;
    }

    .modal-step {
        display: none;
        animation: modalFadeIn 0.5s ease-out;
    }
    .modal-step.active { display: block; }
    @keyframes modalFadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .close-modal {
        position: absolute;
        right: 30px;
        top: 25px;
        font-size: 26px;
        color: #ccc;
        cursor: pointer;
        transition: 0.3s;
        z-index: 10;
    }
    .close-modal:hover { color: var(--color-forest-green); transform: rotate(90deg); }

    .modal-header-modern-inner {
        margin-bottom: 30px;
    }
    .modal-logo-small {
        width: 70px;
        height: auto;
        margin-bottom: 15px;
    }
    .modal-header-modern-inner h3 {
        color: var(--color-forest-green);
        margin: 0;
        font-size: 24px;
        font-weight: 800;
    }
    .modal-header-modern-inner p {
        color: #666;
        font-size: 14px;
        margin-top: 10px;
        line-height: 1.6;
    }

    .input-modal-premium {
        margin-bottom: 25px;
    }
    .input-modal-premium input {
        width: 100%;
        padding: 16px 20px;
        background: #fff;
        border: 1.5px solid #eee;
        border-radius: 16px;
        font-size: 15px;
        outline: none;
        text-align: center;
        transition: 0.3s ease;
        box-sizing: border-box;
    }
    .input-modal-premium input:focus {
        border-color: var(--color-forest-green);
        box-shadow: 0 0 0 5px rgba(27, 58, 26, 0.08);
    }

    .btn-modal-forest {
        width: 100%;
        padding: 16px;
        background: var(--color-forest-green);
        color: #fff;
        border: none;
        border-radius: 50px;
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 10px 20px rgba(27, 58, 26, 0.2);
    }
    .btn-modal-forest:hover {
        background: #254d24;
        transform: translateY(-2px);
        box-shadow: 0 12px 25px rgba(27, 58, 26, 0.3);
    }

    .code-input-premium {
        font-size: 28px !important;
        letter-spacing: 12px !important;
        font-weight: 800 !important;
        color: var(--color-forest-green) !important;
        text-indent: 12px;
    }

    .password-modal-container {
        position: relative;
        margin-bottom: 15px;
    }
    .password-modal-container .toggle-password { right: 20px; }

    .alert-modal-premium {
        padding: 14px;
        border-radius: 12px;
        margin-bottom: 25px;
        font-size: 13px;
        font-weight: 600;
        display: none;
    }
    .alert-modal-premium-error { background: #fee2e2; color: #b91c1c; border-left: 4px solid #b91c1c; }
    .alert-modal-premium-success { background: #ecfdf5; color: #059669; border-left: 4px solid #059669; }

    @media (max-width: 480px) {
        .modal-content-custom { padding: 40px 25px; }
    }

    /* Tambahkan CSS ini di bagian bawah style yang sudah ada di login.blade.php */
/* Responsive Styles - Tanpa Refresh (langsung responsif) */

/* Tablet Landscape & Portrait (768px - 1024px) */
@media screen and (max-width: 1024px) and (min-width: 769px) {
    .login-card-modern {
        max-width: 450px;
        padding: 28px 25px;
    }

    .img-logo-modern {
        width: 60px;
    }

    .brand-name-modern .brand-naz {
        font-size: 20px;
    }

    .brand-name-modern .brand-hidro {
        font-size: 10px;
        letter-spacing: 1.5px;
    }

    .form-title {
        font-size: 19px;
    }

    .form-subtitle {
        font-size: 12.5px;
    }

    .input-group-modern input {
        padding: 14px 45px;
        font-size: 13.5px;
    }

    .btn-login-modern {
        padding: 14px;
        font-size: 15px;
    }

    .login-utilities {
        margin-bottom: 18px;
    }

    .remember-checkbox,
    .forgot-link {
        font-size: 12px;
    }
}

/* Mobile Landscape (481px - 768px) */
@media screen and (max-width: 768px) and (min-width: 481px) {
    .auth-page-container {
        padding: 30px 15px;
    }

    .login-card-modern {
        max-width: 420px;
        padding: 25px 22px;
        border-radius: 24px;
    }

    .img-logo-modern {
        width: 55px;
    }

    .brand-name-modern .brand-naz {
        font-size: 19px;
    }

    .brand-name-modern .brand-hidro {
        font-size: 9.5px;
        letter-spacing: 1.5px;
    }

    .form-title {
        font-size: 18px;
    }

    .form-subtitle {
        font-size: 12px;
        margin: 5px 0 18px;
    }

    .input-group-modern input {
        padding: 13px 42px;
        font-size: 13px;
        border-radius: 14px;
    }

    .input-group-modern .input-icon {
        left: 15px;
        font-size: 13px;
    }

    .toggle-password {
        right: 15px;
    }

    .btn-login-modern {
        padding: 13px;
        font-size: 14.5px;
        border-radius: 45px;
    }

    .login-utilities {
        margin-bottom: 16px;
    }

    .remember-checkbox {
        font-size: 11.5px;
    }

    .forgot-link {
        font-size: 11.5px;
    }

    .form-footer-modern {
        margin-top: 22px;
        font-size: 12px;
    }

    .back-link {
        font-size: 12px;
        margin-top: 12px;
    }

    .error-simple-text {
        font-size: 12px;
        margin-bottom: 18px;
    }

    /* Modal Responsive */
    .modal-content-custom {
        max-width: 380px;
        padding: 40px 30px;
    }

    .modal-logo-small {
        width: 60px;
    }

    .modal-header-modern-inner h3 {
        font-size: 22px;
    }

    .modal-header-modern-inner p {
        font-size: 13px;
    }

    .input-modal-premium input {
        padding: 14px 18px;
        font-size: 14px;
    }

    .btn-modal-forest {
        padding: 14px;
        font-size: 15px;
    }

    .code-input-premium {
        font-size: 24px !important;
        letter-spacing: 8px !important;
    }
}

/* Mobile Portrait (320px - 480px) */
@media screen and (max-width: 480px) {
    .auth-page-container {
        padding: 20px 12px;
        align-items: flex-start;
        min-height: 100vh;
        padding-top: 40px;
        padding-bottom: 40px;
    }

    .login-card-modern {
        max-width: 100%;
        padding: 20px 16px;
        border-radius: 20px;
    }

    .card-header-modern {
        margin-bottom: 15px;
    }

    .logo-wrapper-modern {
        margin-bottom: 5px;
    }

    .img-logo-modern {
        width: 48px;
    }

    .brand-name-modern .brand-naz {
        font-size: 18px;
    }

    .brand-name-modern .brand-hidro {
        font-size: 9px;
        letter-spacing: 1px;
        margin-top: -2px;
    }

    .form-title {
        font-size: 17px;
    }

    .form-subtitle {
        font-size: 11px;
        margin: 4px 0 16px;
    }

    .input-container-modern {
        margin-bottom: 12px;
    }

    .input-group-modern {
        margin-bottom: 10px;
    }

    .input-group-modern .input-icon {
        left: 14px;
        font-size: 12px;
    }

    .input-group-modern input {
        padding: 12px 38px;
        font-size: 12.5px;
        border-radius: 12px;
    }

    .toggle-password {
        right: 14px;
        font-size: 12px;
    }

    .login-utilities {
        margin-bottom: 14px;
        padding: 0 2px;
    }

    .remember-checkbox {
        font-size: 11px;
    }

    .remember-checkbox input {
        width: 15px;
        height: 15px;
        margin-right: 6px;
    }

    .forgot-link {
        font-size: 11px;
    }

    .btn-login-modern {
        padding: 12px;
        font-size: 14px;
        border-radius: 40px;
    }

    .form-footer-modern {
        margin-top: 18px;
        font-size: 11px;
    }

    .back-link {
        font-size: 11px;
        margin-top: 10px;
    }

    .error-simple-text {
        font-size: 11px;
        margin-bottom: 15px;
    }

    /* Modal Responsive Mobile */
    .modal-custom {
        padding: 10px;
    }

    .modal-content-custom {
        max-width: 100%;
        padding: 30px 20px;
        border-radius: 24px;
    }

    .close-modal {
        right: 20px;
        top: 18px;
        font-size: 22px;
    }

    .modal-logo-small {
        width: 55px;
        margin-bottom: 10px;
    }

    .modal-header-modern-inner {
        margin-bottom: 20px;
    }

    .modal-header-modern-inner h3 {
        font-size: 20px;
    }

    .modal-header-modern-inner p {
        font-size: 12px;
        margin-top: 8px;
    }

    .input-modal-premium {
        margin-bottom: 20px;
    }

    .input-modal-premium input {
        padding: 12px 16px;
        font-size: 13px;
        border-radius: 14px;
    }

    .btn-modal-forest {
        padding: 12px;
        font-size: 14px;
        border-radius: 45px;
    }

    .code-input-premium {
        font-size: 20px !important;
        letter-spacing: 6px !important;
        text-indent: 10px;
    }

    .alert-modal-premium {
        padding: 10px;
        font-size: 11px;
        margin-bottom: 20px;
    }

    .password-modal-container input {
        font-size: 13px;
    }

    #stepSuccess .modal-header-modern-inner div {
        font-size: 60px;
    }

    #stepSuccess .modal-header-modern-inner h3 {
        font-size: 18px;
    }

    #stepSuccess .modal-header-modern-inner p {
        font-size: 11px;
    }

    #stepSuccess .btn-modal-forest {
        margin-top: 30px;
        padding: 12px;
        font-size: 13px;
    }

    /* Resend code link */
    .modal-step p[style*="text-align: center"] {
        font-size: 11px !important;
        margin-top: 15px !important;
    }
}

/* Extra Small Devices (di bawah 360px) */
@media screen and (max-width: 360px) {
    .auth-page-container {
        padding: 15px 10px;
        padding-top: 30px;
    }

    .login-card-modern {
        padding: 16px 14px;
    }

    .img-logo-modern {
        width: 42px;
    }

    .brand-name-modern .brand-naz {
        font-size: 16px;
    }

    .brand-name-modern .brand-hidro {
        font-size: 8px;
    }

    .form-title {
        font-size: 16px;
    }

    .form-subtitle {
        font-size: 10px;
    }

    .input-group-modern input {
        padding: 11px 35px;
        font-size: 12px;
    }

    .btn-login-modern {
        padding: 11px;
        font-size: 13px;
    }

    .modal-content-custom {
        padding: 25px 16px;
    }

    .modal-header-modern-inner h3 {
        font-size: 18px;
    }

    .code-input-premium {
        font-size: 18px !important;
        letter-spacing: 4px !important;
    }
}

.error-lockout-box {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    background: #fff8e1;
    border: 1px solid #ffe082;
    border-left: 4px solid #f59e0b;
    border-radius: 12px;
    padding: 14px 16px;
    margin-bottom: 16px;
    text-align: left;
}
.lockout-icon {
    font-size: 22px;
    color: #f59e0b;
    flex-shrink: 0;
    margin-top: 2px;
}
.lockout-text strong {
    display: block;
    font-size: 13.5px;
    color: #92400e;
    margin-bottom: 4px;
}
.lockout-text p {
    margin: 0;
    font-size: 12.5px;
    color: #78350f;
    line-height: 1.5;
}
</style>

<!-- Modal Lupa Password Premium -->
<div id="modalLupaPassword" class="modal-custom">
    <div class="modal-content-custom">
        <span class="close-modal" id="closeModal">&times;</span>

        <!-- Logo di Setiap Modal -->
        <div class="modal-header-modern-inner">
            <img src="{{ asset('storage/image/logo.png') }}" alt="Logo" class="modal-logo-small">
        </div>

        <!-- Step 1: Input Email -->
        <div id="stepEmail" class="modal-step active">
            <div class="modal-header-modern-inner" style="margin-top: -15px;">
                <h3>Lupa Password?</h3>
                <p>Masukkan email yang terdaftar untuk menerima kode unik verifikasi.</p>
            </div>
            <div id="alertStep1" class="alert-modal-premium alert-modal-premium-error"></div>
            <div class="input-modal-premium">
                <input type="email" id="resetEmail" placeholder="Masukkan alamat email anda" required>
            </div>
            <button type="button" class="btn-modal-forest" id="btnKirimKode">Kirim Kode</button>
        </div>

        <!-- Step 2: Input Kode -->
        <div id="stepCode" class="modal-step">
            <div class="modal-header-modern-inner" style="margin-top: -15px;">
                <h3>Verifikasi Kode</h3>
                <p>Masukkan 6 digit kode unik yang telah kami kirimkan ke email Anda.</p>
            </div>
            <div id="alertStep2" class="alert-modal-premium alert-modal-premium-error"></div>
            <div class="input-modal-premium">
                <input type="text" id="resetCode" maxlength="6" class="code-input-premium" placeholder="000000">
            </div>
            <button type="button" class="btn-modal-forest" id="btnVerifikasiKode">Verifikasi Sekarang</button>
            <p style="text-align: center; margin-top: 20px; font-size: 13px; color: #888;">
                Belum menerima email? <a href="javascript:void(0)" id="btnResendCode" style="color: var(--color-forest-green); font-weight: 800; text-decoration: none;">Kirim ulang kode</a>
            </p>
        </div>

        <!-- Step 3: Password Baru -->
        <div id="stepNewPassword" class="modal-step">
            <div class="modal-header-modern-inner" style="margin-top: -15px;">
                <h3>Buat Password Baru</h3>
                <p>Silakan buat kata sandi yang kuat dan aman untuk akun Anda.</p>
            </div>
            <div id="alertStep3" class="alert-modal-premium alert-modal-premium-error"></div>
            <div class="password-modal-container input-modal-premium">
                <input type="password" id="newPassword" placeholder="Password Baru" required>
                <i class="fas fa-eye toggle-password" id="toggleNewPw"></i>
            </div>
            <p style="font-size: 11px; color: #888; margin-top: -15px; margin-bottom: 15px; text-align: center;"><i class="fas fa-info-circle"></i> Minimal 7 karakter, harus ada huruf besar, angka, dan simbol (!@#$%^&*).</p>
            <div class="password-modal-container input-modal-premium">
                <input type="password" id="confirmNewPassword" placeholder="Konfirmasi Password Baru" required>
                <i class="fas fa-eye toggle-password" id="toggleConfirmPw"></i>
            </div>
            <button type="button" class="btn-modal-forest" id="btnSelesaiReset">Simpan Password</button>
        </div>

        <!-- Step 4: Berhasil -->
        <div id="stepSuccess" class="modal-step">
            <div class="modal-header-modern-inner" style="margin-bottom: 0; padding: 20px 0;">
                <div style="font-size: 80px; color: #10b981; margin-bottom: 25px; filter: drop-shadow(0 10px 15px rgba(16, 185, 129, 0.2));"><i class="fas fa-check-circle"></i></div>
                <h3 style="color: #10b981;">Berhasil Diperbarui!</h3>
                <p>Password Anda telah berhasil diatur ulang. Kini Anda dapat masuk kembali.</p>
                <button type="button" class="btn-modal-forest" style="margin-top: 40px; background: #10b981; box-shadow: 0 10px 20px rgba(16, 185, 129, 0.2);" onclick="location.reload()">Login Sekarang</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle Password Visibility Global
        function setupToggle(buttonId, fieldId) {
            const btn = document.querySelector(buttonId);
            const field = document.querySelector(fieldId);
            if (btn && field) {
                btn.onclick = function() {
                    const type = field.getAttribute('type') === 'password' ? 'text' : 'password';
                    field.setAttribute('type', type);
                    this.classList.toggle('fa-eye');
                    this.classList.toggle('fa-eye-slash');
                }
            }
        }
        setupToggle('#togglePassword', '#password');
        setupToggle('#toggleNewPw', '#newPassword');
        setupToggle('#toggleConfirmPw', '#confirmNewPassword');

        // Modal Logic
        const modal = document.getElementById('modalLupaPassword');
        const btnLupa = document.getElementById('btnLupaPassword');
        const closeBtn = document.getElementById('closeModal');

        btnLupa.onclick = () => {
            modal.style.display = 'flex';
            modal.style.opacity = '0';
            setTimeout(() => modal.style.opacity = '1', 50);
        }
        closeBtn.onclick = () => {
            modal.style.opacity = '0';
            setTimeout(() => {
                modal.style.display = 'none';
                resetModal();
            }, 300);
        }
        window.onclick = (e) => {
            if(e.target == modal) {
                modal.style.opacity = '0';
                setTimeout(() => {
                    modal.style.display = 'none';
                    resetModal();
                }, 300);
            }
        }

        function switchStep(fromId, toId) {
            const from = document.getElementById(fromId);
            const to = document.getElementById(toId);
            from.style.opacity = '0';
            from.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                from.classList.remove('active');
                to.classList.add('active');
                to.style.opacity = '0';
                to.style.transform = 'translateY(10px)';
                setTimeout(() => {
                    to.style.opacity = '1';
                    to.style.transform = 'translateY(0)';
                }, 50);
            }, 300);
        }

        function resetModal() {
            document.querySelectorAll('.modal-step').forEach(s => s.classList.remove('active'));
            document.getElementById('stepEmail').classList.add('active');
            document.querySelectorAll('.alert-modal-premium').forEach(a => a.style.display = 'none');
            document.querySelectorAll('.modal-step input').forEach(i => i.value = '');
        }

        function showAlert(id, msg, type = 'error') {
            const alert = document.getElementById(id);
            alert.innerText = msg;
            alert.style.display = 'block';
            alert.className = `alert-modal-premium alert-modal-premium-${type}`;
        }

        // step 1: Send Code
        document.getElementById('btnKirimKode').onclick = async function() {
            const email = document.getElementById('resetEmail').value;
            if(!email) return showAlert('alertStep1', 'Silakan masukkan alamat email anda.');

            this.disabled = true;
            this.innerText = 'Memproses...';

            try {
                const response = await fetch("{{ route('password.send-code') }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ email })
                });
                const data = await response.json();

                if(data.success) {
                    switchStep('stepEmail', 'stepCode');
                } else {
                    showAlert('alertStep1', data.message);
                }
            } catch (error) {
                showAlert('alertStep1', 'Terjadi kesalahan sistem.');
            } finally {
                this.disabled = false;
                this.innerText = 'Kirim Kode';
            }
        }

        // step 2: Verify Code
        document.getElementById('btnVerifikasiKode').onclick = async function() {
            const email = document.getElementById('resetEmail').value;
            const code = document.getElementById('resetCode').value;
            if(!code) return showAlert('alertStep2', 'Masukkan 6 digit kode verifikasi.');

            this.disabled = true;
            try {
                const response = await fetch("{{ route('password.verify-code') }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ email, code })
                });
                const data = await response.json();

                if(data.success) {
                    switchStep('stepCode', 'stepNewPassword');
                } else {
                    showAlert('alertStep2', data.message);
                }
            } catch (error) {
                showAlert('alertStep2', 'Gagal verifikasi.');
            } finally {
                this.disabled = false;
            }
        }

        // step 3: Reset Password
        document.getElementById('btnSelesaiReset').onclick = async function() {
            const email = document.getElementById('resetEmail').value;
            const code = document.getElementById('resetCode').value;
            const password = document.getElementById('newPassword').value;
            const password_confirmation = document.getElementById('confirmNewPassword').value;

            if(!password || password.length < 7) return showAlert('alertStep3', 'Password minimal harus 7 karakter.');
            if(password !== password_confirmation) return showAlert('alertStep3', 'Konfirmasi kata sandi tidak cocok.');

            this.disabled = true;
            try {
                const response = await fetch("{{ route('password.reset') }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ email, code, password, password_confirmation })
                });
                const data = await response.json();

                if(data.success) {
                    switchStep('stepNewPassword', 'stepSuccess');
                } else {
                    showAlert('alertStep3', data.message);
                }
            } catch (error) {
                showAlert('alertStep3', 'Gagal merubah password.');
            } finally {
                this.disabled = false;
            }
        }

        // Resend Code
        document.getElementById('btnResendCode').onclick = function() {
            document.getElementById('btnKirimKode').click();
            showAlert('alertStep2', 'Kode verifikasi baru telah dikirim!', 'success');
        }
    });
</script>

@endsection
