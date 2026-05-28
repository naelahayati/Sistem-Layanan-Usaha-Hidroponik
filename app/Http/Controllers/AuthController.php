<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AuthController extends Controller
{
    const MAX_ATTEMPTS  = 5;
    const LOCKOUT_HOURS = 24;

    public function login()
    {
        return view('auth.login');
    }

    public function register()
    {
        return view('auth.Register');
    }

    public function doregister(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'string',
                'min:7',
                function ($attribute, $value, $fail) {
                    if (!preg_match('/[a-z]/', $value)) { $fail('Password harus mengandung huruf kecil.'); }
                    if (!preg_match('/[A-Z]/', $value)) { $fail('Password harus mengandung huruf besar.'); }
                    if (!preg_match('/[0-9]/', $value)) { $fail('Password harus mengandung angka.'); }
                    if (!preg_match('/[!@#$%^&*]/', $value)) { $fail('Password harus mengandung simbol (!@#$%^&*).'); }
                }
            ],
            'alamat'    => 'required|string',
            'nohp'      => 'required|numeric',
            'umur'      => 'required|numeric|min:15',
            'latitude'  => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ], [
            'name.required'     => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique'   => 'Username sudah digunakan, silakan cari yang lain.',
            'email.required'    => 'Alamat email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'email.unique'      => 'Email sudah terdaftar, silakan gunakan email lain.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal harus 7 karakter.',
            'alamat.required'   => 'Alamat wajib diisi.',
            'nohp.required'     => 'Nomor HP wajib diisi.',
            'nohp.numeric'      => 'Nomor HP hanya boleh berisi angka.',
            'umur.required'     => 'Umur wajib diisi.',
            'umur.numeric'      => 'Umur harus berupa angka.',
            'umur.min'          => 'Umur pendaftar minimal harus 15 tahun.',
        ]);

        User::create([
            'name'      => $request->name,
            'username'  => $request->username,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'alamat'    => $request->alamat,
            'nohp'      => $request->nohp,
            'umur'      => $request->umur,
            'latitude'  => $request->latitude,
            'longitude' => $request->longitude,
            'role'      => 'user',
            'status'    => 'active',
        ]);

        return redirect('/login')->with('success', 'Pendaftaran berhasil! Silakan login.');
    }

    public function dologin(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ], [
            'username.required' => 'Username atau Email tidak boleh kosong.',
            'password.required' => 'Password tidak boleh kosong.',
        ]);

        $user = User::where('username', $credentials['username'])
                    ->orWhere('email', $credentials['username'])
                    ->first();

        // User tidak ditemukan di sistem
        if (!$user) {
            return back()->withErrors([
                'username' => 'Username/Email atau password salah.',
            ])->onlyInput('username');
        }

        // Cek akun sedang terkunci
        if ($user->locked_until && Carbon::now()->lt($user->locked_until)) {
            $jamBisa     = $user->locked_until->format('H:i');
            $tanggalBisa = $user->locked_until->translatedFormat('d F Y');

            return back()->withErrors([
                'lockout' => "Terlalu banyak percobaan login gagal. Kamu bisa login kembali pada pukul {$jamBisa} tanggal {$tanggalBisa}.",
            ])->onlyInput('username');
        }

        // Reset otomatis kalau lockout sudah lewat
        if ($user->locked_until && Carbon::now()->gte($user->locked_until)) {
            $user->update(['login_attempts' => 0, 'locked_until' => null]);
        }

        // Login berhasil
        if (Hash::check($credentials['password'], $user->password)) {
            $user->update(['login_attempts' => 0, 'locked_until' => null]);

            Auth::login($user, $request->has('remember'));
            $request->session()->regenerate();

            if ($user->role === 'admin') {
                $request->session()->put('is_admin', true);
                return redirect()->route('admin.dashboard');
            }

            return redirect()->intended('/');
        }

        // Login gagal - tambah percobaan
        $attempts      = $user->login_attempts + 1;
        $sisaPercobaan = self::MAX_ATTEMPTS - $attempts;

        if ($attempts >= self::MAX_ATTEMPTS) {
            $lockedUntil = Carbon::now()->addHours(self::LOCKOUT_HOURS);
            $user->update([
                'login_attempts' => $attempts,
                'locked_until'   => $lockedUntil,
            ]);

            $jamBisa     = $lockedUntil->format('H:i');
            $tanggalBisa = $lockedUntil->translatedFormat('d F Y');

            return back()->withErrors([
                'lockout' => "Terlalu banyak percobaan login gagal. Kamu bisa login kembali pada pukul {$jamBisa} tanggal {$tanggalBisa}.",
            ])->onlyInput('username');
        }

        $user->update(['login_attempts' => $attempts]);

        if ($sisaPercobaan === 1) {
            return back()->withErrors([
                'username' => 'Password salah. Sisa percobaan: 1 kali lagi! Jika lupa password, gunakan fitur "Lupa Password?" sebelum akun kamu tidak bisa login selama 24 jam.',
            ])->onlyInput('username');
        }

        return back()->withErrors([
            'username' => "Username/Email atau password salah. Sisa percobaan: {$sisaPercobaan} kali.",
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $request->session()->forget('is_admin');

        return redirect('/')->with('success', 'Anda telah berhasil logout.');
    }

    // Untuk admin buka kunci akun manual
    public function unlockAccount(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        $user->update(['login_attempts' => 0, 'locked_until' => null]);

        return back()->with('success', "Akun {$user->username} berhasil dibuka kembali.");
    }
}
