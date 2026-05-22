<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Menampilkan halaman login
    public function login()
    {
        return view('auth.login');
    }

    // Menampilkan halaman register
    public function register()
    {
        return view('auth.Register');
    }

    // Proses Registrasi
    public function doregister(Request $request)
    {
        // 1. Validasi data dengan pesan kustom Bahasa Indonesia
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
            'alamat'   => 'required|string',
            'nohp'     => 'required|string|max:20',
            'umur'     => 'required|numeric|min:15',
            'latitude' => 'nullable|numeric',
            'longitude'=> 'nullable|numeric',
        ], [
            // Pesan Error Bahasa Indonesia
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
            'umur.required'     => 'Umur wajib diisi.',
            'umur.numeric'      => 'Umur harus berupa angka.',
            'umur.min'          => 'Umur pendaftar minimal harus 15 tahun.',
        ]);

        // 2. Simpan ke database nazfram (tabel users)
        User::create([
            'name'     => $request->name,
            'username' => $request->username,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'alamat'   => $request->alamat,
            'nohp'     => $request->nohp,
            'umur'     => $request->umur,
            'latitude' => $request->latitude,
            'longitude'=> $request->longitude,
            'role'     => 'user',   // Default role
            'status'   => 'active', // Default status
        ]);

        // 3. Redirect ke login dengan pesan sukses
        return redirect('/login')->with('success', 'Pendaftaran berhasil! Silakan login.');
    }

    // Proses Login
    public function dologin(Request $request)
    {
        // Validasi login
        $credentials = $request->validate([
            'username' => ['required'], // Field ini bisa berisi username atau email
            'password' => ['required'],
        ], [
            'username.required' => 'Username atau Email tidak boleh kosong.',
            'password.required' => 'Password tidak boleh kosong.',
        ]);

        // Coba cari user berdasarkan username atau email
        $user = User::where('username', $credentials['username'])
                    ->orWhere('email', $credentials['username'])
                    ->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            // Login berhasil
            Auth::login($user, $request->has('remember'));
            $request->session()->regenerate();

            // Set session is_admin jika user adalah admin
            if ($user->role === 'admin') {
                $request->session()->put('is_admin', true);
                return redirect()->route('admin.dashboard');
            }

            return redirect()->intended('/');
        }

        // Jika gagal login
        return back()->withErrors([
            'username' => 'Username/Email atau password salah.',
        ])->onlyInput('username');
    }

    // Proses Logout
public function logout(Request $request)
{
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    $request->session()->forget('is_admin');

    return redirect('/')->with('success', 'Anda telah berhasil logout.');
}
}
