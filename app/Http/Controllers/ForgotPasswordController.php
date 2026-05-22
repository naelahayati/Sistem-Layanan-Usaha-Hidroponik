<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendCodeResetPassword;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    // Mengirim kode verifikasi ke email
    public function sendCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.exists' => 'Email tidak terdaftar di sistem kami.',
        ]);

        $email = $request->email;
        $code = rand(100000, 999999);

        // Hapus token lama jika ada
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        // Simpan token baru
        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => $code,
            'created_at' => Carbon::now(),
        ]);

        // Kirim Email
        try {
            Mail::to($email)->send(new SendCodeResetPassword($code));
            return response()->json(['success' => true, 'message' => 'Kode unik telah dikirim ke email Anda.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengirim email. Silakan coba lagi nanti.'], 500);
        }
    }

    // Verifikasi kode yang dimasukkan user
    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'code' => 'required|numeric',
        ]);

        $reset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->code)
            ->first();

        if (!$reset) {
            return response()->json(['success' => false, 'message' => 'Kode verifikasi salah.'], 400);
        }

        // Cek kedaluwarsa (15 menit)
        if (Carbon::parse($reset->created_at)->addMinutes(15)->isPast()) {
            return response()->json(['success' => false, 'message' => 'Kode verifikasi telah kedaluwarsa.'], 400);
        }

        return response()->json(['success' => true, 'message' => 'Kode berhasil diverifikasi.']);
    }

    // Reset password ke yang baru
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'code' => 'required|numeric',
            'password' => [
                'required',
                'string',
                'min:7',
                'confirmed',
                function ($attribute, $value, $fail) {
                    if (!preg_match('/[a-z]/', $value)) { $fail('Password baru harus mengandung huruf kecil.'); }
                    if (!preg_match('/[A-Z]/', $value)) { $fail('Password baru harus mengandung huruf besar.'); }
                    if (!preg_match('/[0-9]/', $value)) { $fail('Password baru harus mengandung angka.'); }
                    if (!preg_match('/[!@#$%^&*]/', $value)) { $fail('Password baru harus mengandung simbol (!@#$%^&*).'); }
                }
            ],
        ], [
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password minimal harus 7 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        // Verifikasi ulang kode sebelum reset (keamanan tambahan)
        $reset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->code)
            ->first();

        if (!$reset || Carbon::parse($reset->created_at)->addMinutes(15)->isPast()) {
            return response()->json(['success' => false, 'message' => 'Sesi reset password tidak valid atau telah kedaluwarsa.'], 400);
        }

        // Update Password
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Hapus token setelah berhasil reset
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['success' => true, 'message' => 'Password berhasil diubah. Silakan login.']);
    }
}
