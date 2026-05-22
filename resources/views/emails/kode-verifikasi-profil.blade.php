<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kode Verifikasi - Naz Hidrofarm</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f7f4; margin: 0; padding: 20px; color: #333; }
        .wrapper { max-width: 560px; margin: auto; background: #fff; border-radius: 14px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #2d5a27, #4a8c3f); padding: 32px 30px; text-align: center; }
        .header img { width: 52px; margin-bottom: 10px; }
        .header h2 { margin: 0; color: #fff; font-size: 1.5rem; font-weight: 700; letter-spacing: -0.3px; }
        .header p { margin: 6px 0 0; color: rgba(255,255,255,0.8); font-size: 0.9rem; }
        .body { padding: 36px 30px; }
        .body p { font-size: 0.97rem; line-height: 1.7; color: #444; margin: 0 0 14px; }
        .code-box { text-align: center; margin: 28px 0; }
        .code-box .code {
            display: inline-block;
            font-size: 2.4rem;
            font-weight: 800;
            letter-spacing: 10px;
            color: #2d5a27;
            background: #f0f8ee;
            border: 2px dashed #a8d5a2;
            padding: 16px 32px;
            border-radius: 12px;
        }
        .info-box { background: #fffbea; border-left: 4px solid #f0b429; border-radius: 6px; padding: 14px 18px; margin: 20px 0; font-size: 0.88rem; color: #7a5c0a; }
        .footer { background: #f8fafb; border-top: 1px solid #eee; padding: 20px 30px; text-align: center; font-size: 0.8rem; color: #999; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h2>Naz Hidrofarm</h2>
            <p>Kode Verifikasi Akun</p>
        </div>
        <div class="body">
            <p>Halo,</p>
            <p>
                Kami menerima permintaan untuk mengubah
                <strong>{{ $jenis === 'email' ? 'alamat email' : 'password' }}</strong>
                pada akun Naz Hidrofarm Anda.
            </p>
            <p>Gunakan kode verifikasi berikut untuk melanjutkan proses:</p>

            <div class="code-box">
                <span class="code">{{ $code }}</span>
            </div>

            <div class="info-box">
                ⏱ Kode ini hanya berlaku selama <strong>15 menit</strong>. Jika Anda tidak merasa melakukan
                permintaan ini, silakan abaikan email ini dan pastikan akun Anda aman.
            </div>

            <p>Terima kasih,<br><strong>Tim Naz Hidrofarm</strong></p>
        </div>
        <div class="footer">
            Email ini dikirim secara otomatis. Mohon tidak membalas email ini.<br>
            &copy; {{ date('Y') }} Naz Hidrofarm. All rights reserved.
        </div>
    </div>
</body>
</html>
