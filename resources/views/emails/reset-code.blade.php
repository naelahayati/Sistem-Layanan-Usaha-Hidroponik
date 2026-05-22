<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kode Reset Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 5px;
        }
        .code {
            font-size: 24px;
            font-weight: bold;
            color: #2d5a27;
            background: #f4f4f4;
            padding: 10px 20px;
            display: inline-block;
            margin: 10px 0;
            border-radius: 4px;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #777;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h3>Halo,</h3>
        <p>Kami menerima permintaan untuk melakukan pengantian password akun Naz Hidrofarm Anda.</p>
        <p>Gunakan kode verifikasi di bawah ini untuk melanjutkan proses reset password:</p>
        
        <div class="code">{{ $code }}</div>
        
        <p>Kode ini hanya berlaku selama 15 menit. Jika Anda tidak merasa melakukan permintaan ini, silakan abaikan email ini.</p>
        
        <p>Terima kasih,<br>Tim Naz Hidrofarm</p>
        
        <div class="footer">
            Email ini dikirim secara otomatis, mohon tidak membalas email ini.
        </div>
    </div>
</body>
</html>
