<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Kunjungan;
use App\Models\Jadwal;
use App\Models\Magang;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Mail\SendKodeVerifikasiProfil;
use Illuminate\Support\Facades\Auth;
use App\Services\PendaftaranMagangService;
use App\Services\ReservasiKunjunganService;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class Nazframcontroller extends Controller
{
    // private function initMidtrans()
    // {
    //     $this->initMidtrans();
    //     \Midtrans\Config::$serverKey = config('midtrans.server_key');
    //     \Midtrans\Config::$isProduction = false;
    //     \Midtrans\Config::$isSanitized = true;
    //     \Midtrans\Config::$is3ds = true;
    // }

    public function midtransCallback(Request $request)
    {
        \Log::info("Webhook Midtrans Masuk: " . json_encode($request->all()));

        $serverKey = config('midtrans.server_key');
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        // Security check
        if ($hashed == $request->signature_key) {
            $orderId = $request->order_id;
            $status = $request->transaction_status;

            if ($status == 'settlement' || $status == 'capture') {
                if (str_starts_with($orderId, 'KUNJ-')) {
                    DB::table('reservasi_kunjungan')->where('midtrans_order_id', $orderId)->update(['status_pembayaran' => 'Lunas']);
                } elseif (str_starts_with($orderId, 'MAG-')) {
                    $affected = DB::table('pendaftaran_magang')->where('midtrans_order_id', $orderId)->update(['status_pembayaran' => 'Lunas']);
                    \Log::info("Update Magang untuk $orderId terpengaruh $affected baris.");
                } elseif (str_starts_with($orderId, 'ORD-') || str_starts_with($orderId, 'ORDER-')) {
                    Order::where('midtrans_order_id', $orderId)->update(['status' => 'Diproses']);
                }
            } elseif ($status == 'expire' || $status == 'cancel') {
                if (str_starts_with($orderId, 'KUNJ-')) {
                    DB::table('reservasi_kunjungan')->where('midtrans_order_id', '=', $orderId, 'and')->update(['status_pembayaran' => 'Expired']);
                } elseif (str_starts_with($orderId, 'MAG-')) {
                    DB::table('pendaftaran_magang')->where('midtrans_order_id', '=', $orderId, 'and')->update(['status_pembayaran' => 'Expired']);
                } elseif (str_starts_with($orderId, 'ORD-') || str_starts_with($orderId, 'ORDER-')) {
                    $order = Order::where('midtrans_order_id', '=', $orderId)->first();
                    if ($order && $order->status == 'Menunggu Pembayaran') {
                        $this->restoreToCart($order, 'Expired');
                    }
                }
            }
        }
        return response()->json(['success' => true]);
    }

    private function getOrCreateCart()
    {
        $userId = \Illuminate\Support\Facades\Auth::id();
        $cart = Cart::where("user_id", $userId)->first();
        if (!$cart) {
            $cart = Cart::create([
                "user_id" => $userId,
                "session_id" => session()->getId()
            ]);
        }
        return $cart;
    }

    public function home()
    {
        return view("home");
    }
    public function profil()
    {
        return view("profil");
    }

    public function profilSaya()
    {
        $prev = url()->previous();
        // Hanya simpan jika bukan dari halaman profil itu sendiri (menghindari loop back ke diri sendiri)
        if ($prev && !str_contains($prev, 'profil-saya')) {
            session(['url_sebelum_profil' => $prev]);
        }

        return view("profil_pengguna", ["user" => Auth::user()]);
    }

    // Update data umum (nama, username, alamat, nohp, umur, geo)
    public function updateProfilSaya(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . Auth::id(),
            'alamat' => 'required|string',
            'nohp' => 'required|string|max:20',
            'umur' => 'required|numeric|min:1',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan orang lain.',
            'alamat.required' => 'Alamat wajib diisi.',
            'nohp.required' => 'Nomor HP wajib diisi.',
            'umur.required' => 'Umur wajib diisi.',
            'umur.numeric' => 'Umur harus berupa angka.',
        ]);

        $user = User::find(Auth::id());
        $user->name = $request->name;
        $user->username = $request->username;
        $user->alamat = $request->alamat;
        $user->nohp = $request->nohp;
        $user->umur = $request->umur;
        $user->latitude = $request->latitude;
        $user->longitude = $request->longitude;
        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    // Kirim kode OTP ke email (untuk ubah password atau ubah email)
    public function kirimKodeVerifikasiProfil(Request $request)
    {
        $jenis = $request->jenis; // 'password' atau 'email'
        $user = Auth::user();
        $email = $user->email;

        $code = rand(100000, 999999);

        // Simpan OTP di session (berlaku 15 menit)
        session([
            'profil_otp_code' => $code,
            'profil_otp_jenis' => $jenis,
            'profil_otp_expires' => Carbon::now()->addMinutes(15)->toDateTimeString(),
        ]);

        try {
            Mail::to($email)->send(new SendKodeVerifikasiProfil($code, $jenis));
            return response()->json(['success' => true, 'message' => 'Kode verifikasi telah dikirim ke email Anda.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengirim email. Coba lagi nanti.'], 500);
        }
    }

    // Verifikasi Kode OTP (langkah sebelum muncul input edit)
    public function verifikasiKodeProfil(Request $request)
    {
        $request->validate([
            'kode' => 'required|numeric',
            'jenis' => 'required|in:email,password'
        ]);

        if (!$this->validasiOtpProfil($request->kode, $request->jenis)) {
            return response()->json(['success' => false, 'message' => 'Kode verifikasi salah atau sudah kedaluwarsa.'], 400);
        }

        // Tandai di session bahwa kode sudah diverifikasi untuk proses ini
        session(['profil_otp_verified' => true]);

        return response()->json(['success' => true, 'message' => 'Kode berhasil diverifikasi!']);
    }

    // Ubah Password
    public function ubahPassword(Request $request)
    {
        $request->validate([
            'password_baru' => [
                'required',
                'string',
                'min:7',
                'confirmed',
                function ($attribute, $value, $fail) {
                    if (!preg_match('/[a-z]/', $value)) {
                        $fail('Password baru harus mengandung huruf kecil.');
                    }
                    if (!preg_match('/[A-Z]/', $value)) {
                        $fail('Password baru harus mengandung huruf besar.');
                    }
                    if (!preg_match('/[0-9]/', $value)) {
                        $fail('Password baru harus mengandung angka.');
                    }
                    if (!preg_match('/[!@#$%^&*]/', $value)) {
                        $fail('Password baru harus mengandung simbol (!@#$%^&*).');
                    }
                }
            ],
        ], [
            'password_baru.required' => 'Password baru wajib diisi.',
            'password_baru.min' => 'Password minimal 7 karakter.',
            'password_baru.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        // Keamanan: pastikan sudah verifikasi kode sebelumnya
        if (!session('profil_otp_verified') || session('profil_otp_jenis') !== 'password') {
            return response()->json(['success' => false, 'message' => 'Sesi verifikasi tidak valid.'], 403);
        }

        $user = User::find(Auth::id());
        $user->password = Hash::make($request->password_baru);
        $user->save();

        $this->hapusOtpProfil();
        session()->forget('profil_otp_verified');

        return response()->json(['success' => true, 'message' => 'Password berhasil diubah!']);
    }

    // Ubah Email
    public function ubahEmail(Request $request)
    {
        $request->validate([
            'email_baru' => 'required|email|unique:users,email',
        ], [
            'email_baru.required' => 'Email baru wajib diisi.',
            'email_baru.email' => 'Format email tidak valid.',
            'email_baru.unique' => 'Email sudah digunakan akun lain.',
        ]);

        // Keamanan: pastikan sudah verifikasi kode sebelumnya
        if (!session('profil_otp_verified') || session('profil_otp_jenis') !== 'email') {
            return response()->json(['success' => false, 'message' => 'Sesi verifikasi tidak valid.'], 403);
        }

        $user = User::find(Auth::id());
        $user->email = $request->email_baru;
        $user->save();

        $this->hapusOtpProfil();
        session()->forget('profil_otp_verified');

        return response()->json(['success' => true, 'message' => 'Email berhasil diubah!']);
    }

    // Helper: validasi OTP profil dari session
    private function validasiOtpProfil($kode, $jenis)
    {
        $storedCode = session('profil_otp_code');
        $storedJenis = session('profil_otp_jenis');
        $storedExpiry = session('profil_otp_expires');

        if (!$storedCode || !$storedExpiry)
            return false;
        if ((string) $storedCode !== (string) $kode)
            return false;
        if ($storedJenis !== $jenis)
            return false;
        if (Carbon::parse($storedExpiry)->isPast())
            return false;

        return true;
    }

    // Helper: hapus OTP profil dari session
    private function hapusOtpProfil()
    {
        session()->forget(['profil_otp_code', 'profil_otp_jenis', 'profil_otp_expires']);
    }

    public function produk()
    {
        CartItem::clearExpiredCarts();
        $products = Product::all();
        return view("produk", ["produk" => $products]);
    }

    public function beliProduk($id = null)
    {
        CartItem::clearExpiredCarts();
        $products = Product::all();
        $produkTerpilih = $id ? Product::find($id) : null;

        $cart = $this->getOrCreateCart();
        $cartCount = CartItem::where("cart_id", $cart->id)->sum('quantity');

        return view("beli-produk", [
            "produk" => $products,
            "produkTerpilih" => $produkTerpilih ? collect([$produkTerpilih]) : null,
            "cartCount" => $cartCount
        ]);
    }

    public function storeReservasi(Request $request)
    {
        $request->validate([
            'id_kunjungan' => 'required|exists:kunjungans,id',
            'tanggal_reservasi' => 'required|date|after_or_equal:' . now()->addDays(3)->format('Y-m-d'),
            'jumlah_peserta' => 'required|integer|min:5|max:40',
            'metode_pembayaran' => 'required|in:qris',
            'instansi' => 'required|string',
        ], [
            'id_kunjungan.required' => 'Paket kunjungan wajib dipilih.',
            'tanggal_reservasi.required' => 'Tanggal reservasi wajib diisi.',
            'tanggal_reservasi.after_or_equal' => 'Pendaftaran minimal dilakukan 3 hari sebelum tanggal pelaksanaan.',
            'jumlah_peserta.required' => 'Jumlah peserta wajib diisi.',
            'jumlah_peserta.min' => 'Minimal peserta adalah 5 orang.',
            'jumlah_peserta.max' => 'Maksimal peserta adalah 40 orang.',
            'metode_pembayaran.required' => 'Metode pembayaran wajib dipilih.',
            'instansi.required' => 'Nama instansi atau kelompok wajib diisi.',
        ]);

        // Ambil data paket untuk menghitung total harga
        $kunjungan = Kunjungan::findOrFail($request->id_kunjungan);
        $tanggalReservasi = Carbon::parse($request->tanggal_reservasi)->toDateString();
        $total_harga = $kunjungan->price * $request->jumlah_peserta;

        // Validasi: Cek apakah tanggal sudah dipesan oleh orang lain
        $existing = DB::table('reservasi_kunjungan')
            ->whereDate('tanggal_reservasi', $tanggalReservasi)
            ->whereNotIn('status_pembayaran', ['Dibatalkan', 'Tidak Diterima', 'Expired', 'Dibatalkan Pengguna'])
            ->exists();

        if ($existing) {
            return back()->withInput()->with('error', 'Maaf, tanggal tersebut sudah dipesan (Sold Out). Silakan pilih tanggal lain.');
        }

        // 1. Logika simpan data ke database
        $id_reservasi = DB::table('reservasi_kunjungan')->insertGetId([
            'id_user' => Auth::id(),
            'id_kunjungan' => $request->id_kunjungan,
            'paket_name' => $kunjungan->name,
            'tanggal_reservasi' => $tanggalReservasi,
            'jumlah_peserta' => $request->jumlah_peserta,
            'metode_pembayaran' => $request->metode_pembayaran,
            'total_harga' => $total_harga,
            'instansi' => $request->instansi,
            'status_pembayaran' => 'Menunggu Pembayaran',
            'expires_at' => $request->metode_pembayaran == 'qris' ? now()->addMinutes(25) : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        if ($request->metode_pembayaran == 'qris') {
            return redirect()->route('nazfram.kunjungan.payment', $id_reservasi);
        }

        return redirect()->route('nazfram.kunjungan')
            ->with('success', 'Reservasi berhasil!');

        return redirect()->route('nazfram.kunjungan')
            ->with('success', 'Reservasi berhasil!');
    }

    public function pembayaranKunjungan($id)
    {
        $status = ReservasiKunjunganService::expireIfNeeded((int) $id);
        if ($status === 'Expired') {
            return redirect()->route('nazfram.kunjungan')->with('error', 'Waktu pembayaran QRIS telah habis.');
        }
        if ($status === 'Dibatalkan') {
            return redirect()->route('nazfram.kunjungan')->with('error', 'Reservasi ini telah dibatalkan.');
        }

        $reservasi = DB::table('reservasi_kunjungan')
            ->join('kunjungans', 'reservasi_kunjungan.id_kunjungan', '=', 'kunjungans.id')
            ->where('id_reservasi', '=', $id)
            ->where('id_user', '=', Auth::id())
            ->select('reservasi_kunjungan.*', 'kunjungans.name as paket_name')
            ->first();

        if (!$reservasi)
            abort(404);

        if ($reservasi->status_pembayaran == 'Lunas') {
            return redirect()->route('reservasi.riwayat')->with('success', 'Pembayaran Anda telah berhasil diproses!');
        }

        if ($reservasi->expires_at && Carbon::parse($reservasi->expires_at)->isPast()) {
            return redirect()->route('nazfram.kunjungan')->with('error', 'Waktu pembayaran QRIS telah habis.');
        }

        $qrUrl = \App\Models\Setting::get('qris_image', '');
        $payment_method_active = \App\Models\Setting::get('payment_method_active', 'qris');
        $bank_name = \App\Models\Setting::get('bank_name', '');
        $bank_account_number = \App\Models\Setting::get('bank_account_number', '');
        $bank_account_owner = \App\Models\Setting::get('bank_account_owner', '');

        // Abaikan midtrans logic dan gunakan qris_image dari settings
        if (!$reservasi->midtrans_order_id) {
            $orderId = 'KUNJ-' . $reservasi->id_reservasi . '-' . time();
            DB::table('reservasi_kunjungan')
                ->where('id_reservasi', '=', $id)
                ->update([
                    'midtrans_order_id' => $orderId,
                ]);
        }

        return view('pembayaran_kunjungan', [
            'reservasi' => $reservasi,
            'qrUrl' => $qrUrl,
            'payment_method_active' => $payment_method_active,
            'bank_name' => $bank_name,
            'bank_account_number' => $bank_account_number,
            'bank_account_owner' => $bank_account_owner
        ]);
    }
    public function konfirmasiPembayaranKunjungan(Request $request, $id)
    {
        $request->validate([
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240'
        ]);

        $path = null;
        if ($request->hasFile('bukti_pembayaran')) {
            $path = $request->file('bukti_pembayaran')->store('bukti_pembayaran', 'cloudinary');
            $path = \Illuminate\Support\Facades\Storage::disk('cloudinary')->url($path);
        }

        DB::table('reservasi_kunjungan')
            ->where('id_reservasi', '=', $id)
            ->where('id_user', '=', Auth::id())
            ->update([
                'status_pembayaran' => 'Menunggu Konfirmasi',
                'bukti_pembayaran' => $path,
                'updated_at' => now()
            ]);

        return response()->json(['success' => true, 'message' => 'Bukti pembayaran berhasil dikirim.']);
    }

    public function checkoutMagang($id)
    {
        PendaftaranMagangService::expireIfNeeded((int) $id);

        $pendaftaran = DB::table('pendaftaran_magang')
            ->join('magangs', 'pendaftaran_magang.id_magang', '=', 'magangs.id')
            ->where('id_pendaftaran', '=', $id)
            ->where('id_user', '=', Auth::id())
            ->select('pendaftaran_magang.*', 'magangs.name as paket_name', 'magangs.price as package_price')
            ->first();

        if (!$pendaftaran)
            abort(404);

        if (in_array($pendaftaran->status_pembayaran, ['Lunas', 'Diterima', 'Selesai'])) {
            return redirect()->route('magang.riwayat')->with('success', 'Pendaftaran Anda telah lunas!');
        }

        if (!PendaftaranMagangService::canAccessCheckout($pendaftaran)) {
            return redirect()->route('magang.riwayat')->with('error', 'Pendaftaran belum dikonfirmasi admin atau sudah tidak aktif.');
        }

        // Langsung ke pembayaran tanpa halaman checkout
        // Update status dari Terkonfirmasi ke Menunggu Pembayaran
        if ($pendaftaran->status_pembayaran === 'Terkonfirmasi') {
            $orderId = 'MAG-' . $id . '-' . time();
            $expiresAt = now()->addMinutes(PendaftaranMagangService::PAYMENT_MINUTES);

            \Log::info("Updating checkout magang", [
                'id_pendaftaran' => $id,
                'id_user' => Auth::id(),
                'orderId' => $orderId,
                'expires_at' => $expiresAt
            ]);

            $affectedRows = DB::table('pendaftaran_magang')
                ->where('id_pendaftaran', $id)
                ->where('id_user', Auth::id())
                ->update([
                    'metode_pembayaran' => 'qris',
                    'status_pembayaran' => 'Menunggu Pembayaran',
                    'expires_at' => $expiresAt,
                    'midtrans_order_id' => $orderId,
                    'updated_at' => now(),
                ]);

            \Log::info("Checkout update result", ['affected_rows' => $affectedRows]);

            if (!$affectedRows) {
                return redirect()->route('magang.riwayat')->with('error', 'Gagal memperbarui status pembayaran. Silakan coba lagi.');
            }
        }

        return redirect()->route('nazfram.pembayaran_magang', $id);
    }

    public function processCheckoutMagang(Request $request, $id)
    {
        $request->validate([
            'metode_pembayaran' => 'required|in:qris'
        ]);

        $pendaftaran = DB::table('pendaftaran_magang')
            ->where('id_pendaftaran', '=', $id)
            ->where('id_user', '=', Auth::id())
            ->first();

        if (!$pendaftaran)
            abort(404);

        if (!PendaftaranMagangService::canAccessCheckout($pendaftaran)) {
            return redirect()->route('magang.riwayat')->with('error', 'Pendaftaran belum dikonfirmasi admin.');
        }

        if ($request->metode_pembayaran == 'qris') {
            $orderId = 'MAG-' . $id . '-' . time();
            DB::table('pendaftaran_magang')
                ->where('id_pendaftaran', $id)
                ->update([
                    'metode_pembayaran' => 'qris',
                    'status_pembayaran' => 'Menunggu Pembayaran',
                    'expires_at' => now()->addMinutes(PendaftaranMagangService::PAYMENT_MINUTES),
                    'midtrans_order_id' => $orderId,
                    'updated_at' => now(),
                ]);

            return redirect()->route('nazfram.pembayaran_magang', $id);
        }

        abort(404);
    }

    public function pembayaranMagang($id)
    {
        $status = PendaftaranMagangService::expireIfNeeded((int) $id);
        if ($status === 'Expired') {
            return redirect()->route('magang.riwayat')->with('error', 'Batas waktu pembayaran telah habis. Pendaftaran dibatalkan.');
        }

        $pendaftaran = DB::table('pendaftaran_magang')
            ->join('magangs', 'pendaftaran_magang.id_magang', '=', 'magangs.id')
            ->where('id_pendaftaran', '=', $id)
            ->where('id_user', '=', Auth::id())
            ->select('pendaftaran_magang.*', 'magangs.name as paket_name')
            ->first();

        if (!$pendaftaran)
            abort(404);

        \Log::info("Payment page accessed", [
            'id_pendaftaran' => $id,
            'status_pembayaran' => $pendaftaran->status_pembayaran,
            'metode_pembayaran' => $pendaftaran->metode_pembayaran,
            'expires_at' => $pendaftaran->expires_at,
        ]);

        if (in_array($pendaftaran->status_pembayaran, ['Lunas', 'Diterima'])) {
            return redirect()->route('magang.riwayat')->with('success', 'Pembayaran Anda telah berhasil diproses!');
        }

        // Handle case jika masih Terkonfirmasi (timing issue dari checkout)
        if ($pendaftaran->status_pembayaran === 'Terkonfirmasi' && !$pendaftaran->expires_at) {
            $orderId = 'MAG-' . $id . '-' . time();
            $expiresAt = now()->addMinutes(PendaftaranMagangService::PAYMENT_MINUTES);

            DB::table('pendaftaran_magang')
                ->where('id_pendaftaran', $id)
                ->where('id_user', Auth::id())
                ->update([
                    'metode_pembayaran' => 'qris',
                    'status_pembayaran' => 'Menunggu Pembayaran',
                    'expires_at' => $expiresAt,
                    'midtrans_order_id' => $orderId,
                    'updated_at' => now(),
                ]);

            // Refresh data
            $pendaftaran = DB::table('pendaftaran_magang')
                ->join('magangs', 'pendaftaran_magang.id_magang', '=', 'magangs.id')
                ->where('id_pendaftaran', '=', $id)
                ->where('id_user', '=', Auth::id())
                ->select('pendaftaran_magang.*', 'magangs.name as paket_name')
                ->first();
        }

        if (!PendaftaranMagangService::canAccessPaymentPage($pendaftaran)) {
            \Log::warning("Payment access denied", [
                'id_pendaftaran' => $id,
                'status_pembayaran' => $pendaftaran->status_pembayaran,
                'metode_pembayaran' => $pendaftaran->metode_pembayaran,
                'expires_at' => $pendaftaran->expires_at,
            ]);
            return redirect()->route('magang.riwayat')->with('error', 'Sesi pembayaran tidak valid atau sudah berakhir.');
        }

        $qrUrl = \App\Models\Setting::get('qris_image', '');
        $payment_method_active = \App\Models\Setting::get('payment_method_active', 'qris');
        $bank_name = \App\Models\Setting::get('bank_name', '');
        $bank_account_number = \App\Models\Setting::get('bank_account_number', '');
        $bank_account_owner = \App\Models\Setting::get('bank_account_owner', '');

        // Fallback: Jika benar-benar kosong, buat order id baru tanpa midtrans
        if (!$pendaftaran->midtrans_order_id) {
            $orderId = 'MAG-' . $pendaftaran->id_pendaftaran . '-' . time();
            DB::table('pendaftaran_magang')
                ->where('id_pendaftaran', $id)
                ->update([
                    'midtrans_order_id' => $orderId,
                ]);
        }

        return view('pembayaran_magang', compact(
            'pendaftaran', 'qrUrl', 'payment_method_active',
            'bank_name', 'bank_account_number', 'bank_account_owner'
        ));
    }

    public function konfirmasiPembayaranMagang(Request $request, $id)
    {
        $request->validate([
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240'
        ]);

        $pendaftaran = DB::table('pendaftaran_magang')
            ->where('id_pendaftaran', $id)
            ->where('id_user', Auth::id())
            ->first();

        if (!$pendaftaran || !PendaftaranMagangService::canAccessPaymentPage($pendaftaran)) {
            return response()->json(['success' => false, 'message' => 'Sesi pembayaran tidak valid.'], 422);
        }

        $path = null;
        if ($request->hasFile('bukti_pembayaran')) {
            $path = $request->file('bukti_pembayaran')->store('bukti_pembayaran', 'cloudinary');
            $path = \Illuminate\Support\Facades\Storage::disk('cloudinary')->url($path);
        }

        DB::table('pendaftaran_magang')
            ->where('id_pendaftaran', '=', $id)
            ->where('id_user', '=', Auth::id())
            ->update([
                'status_pembayaran' => 'Menunggu Konfirmasi',
                'bukti_pembayaran' => $path,
                'expires_at' => null,
                'updated_at' => now(),
            ]);

        return response()->json(['success' => true, 'message' => 'Bukti pembayaran berhasil dikirim.']);
    }

    public function expireMagangPayment($id)
    {
        $pendaftaran = DB::table('pendaftaran_magang')
            ->where('id_pendaftaran', $id)
            ->where('id_user', Auth::id())
            ->first();

        if (!$pendaftaran) {
            return response()->json(['success' => false], 404);
        }

        $status = PendaftaranMagangService::expireIfNeeded((int) $id);

        return response()->json([
            'success' => true,
            'status' => $status ?? $pendaftaran->status_pembayaran,
        ]);
    }

    public function magangPaymentStatus($id)
    {
        $pendaftaran = DB::table('pendaftaran_magang')
            ->where('id_pendaftaran', $id)
            ->where('id_user', Auth::id())
            ->first();

        if (!$pendaftaran) {
            return response()->json(['success' => false], 404);
        }

        $status = PendaftaranMagangService::expireIfNeeded((int) $id) ?? $pendaftaran->status_pembayaran;
        $expiresAt = DB::table('pendaftaran_magang')->where('id_pendaftaran', $id)->value('expires_at');

        return response()->json([
            'success' => true,
            'status' => $status,
            'expires_at' => $expiresAt ? Carbon::parse($expiresAt)->toIso8601String() : null,
            'can_pay' => PendaftaranMagangService::canShowPayButton((object) array_merge((array) $pendaftaran, ['status_pembayaran' => $status])),
        ]);
    }

    public function batalMagang($id)
    {
        $pendaftaran = DB::table('pendaftaran_magang')
            ->where('id_pendaftaran', $id)
            ->where('id_user', Auth::id())
            ->first();

        if ($pendaftaran && $pendaftaran->status_pembayaran == 'Menunggu Pembayaran') {
            DB::table('pendaftaran_magang')
                ->where('id_pendaftaran', $id)
                ->update([
                    'status_pembayaran' => 'Dibatalkan Pengguna',
                    'expires_at' => null,
                    'updated_at' => now(),
                ]);
        }

        return response()->json(['success' => true]);
    }

    public function batalKunjungan($id)
    {
        $reservasi = DB::table('reservasi_kunjungan')
            ->where('id_reservasi', '=', $id, 'and')
            ->where('id_user', '=', Auth::id(), 'and')
            ->first();

        if ($reservasi && $reservasi->status_pembayaran == 'Menunggu Pembayaran') {
            DB::table('reservasi_kunjungan')
                ->where('id_reservasi', '=', $id, 'and')
                ->update(['status_pembayaran' => 'Dibatalkan Pengguna']);
        }

        return response()->json(['success' => true]);
    }

    public function kunjungan()
    {
        // Halaman daftar paket (kunjungan.blade.php) - Ambil data dari DB
        $kunjungans = Kunjungan::all();
        return view("kunjungan", compact('kunjungans'));
    }

    // Tambahan method pelatihan agar tidak error
    public function pelatihan()
    {
        $magangs = Magang::all();
        return view("pelatihan", compact('magangs'));
    }

    public function reservasiKunjungan($id = null)
    {
        $kunjungan = Kunjungan::findOrFail($id);
        return view("reservasi_kunjungan", [
            "id_kunjungan" => $id,
            "kunjungan" => $kunjungan
        ]);
    }

    /**
     * Menampilkan riwayat reservasi (Kunjungan atau Magang)
     */
    public function riwayat(Request $request)
    {
        $perPage = $request->has('all') ? 1000 : 8;
        $type = $request->query('type', 'kunjungan');
        $userId = Auth::id();

        if ($type == 'magang') {
            $judul = "Riwayat Pendaftaran Magang";

            // Jalankan auto pembatalan magang (hari H atau lewat yang belum disetujui/dibayar)
            PendaftaranMagangService::autoCancelPassedMagangs();

            $data = DB::table('pendaftaran_magang')
                ->leftJoin('users', 'pendaftaran_magang.id_user', '=', 'users.id')
                ->leftJoin('magangs', 'pendaftaran_magang.id_magang', '=', 'magangs.id')
                ->where('pendaftaran_magang.id_user', '=', $userId)
                ->select('pendaftaran_magang.*', 'magangs.name as paket_name', 'magangs.is_wa_confirmation')
                ->orderBy('pendaftaran_magang.created_at', 'desc')
                ->paginate($perPage);

            foreach ($data as $m) {
                $statusAfterExpire = PendaftaranMagangService::expireIfNeeded((int) $m->id_pendaftaran);
                if ($statusAfterExpire) {
                    $m->status_pembayaran = $statusAfterExpire;
                    if ($statusAfterExpire === 'Expired') {
                        $m->expires_at = null;
                    }
                }

                // 1. Cek Selesai Berdasarkan Tanggal
                $endDate = Carbon::parse($m->tanggal_magang)->addMonths($m->durasi_magang)->endOfDay();
                if (Carbon::today() > $endDate && ($m->status_pembayaran == 'Diterima' || $m->status_pembayaran == 'Lunas')) {
                    DB::table('pendaftaran_magang')
                        ->where('id_pendaftaran', '=', $m->id_pendaftaran, 'and')
                        ->update(['status_pembayaran' => 'Selesai', 'updated_at' => now()]);
                    $m->status_pembayaran = 'Selesai';
                }
            }

            $backRoute = 'nazfram.pelatihan';
            return view('riwayat_pendaftaran', compact('judul', 'data', 'backRoute'));
        } else {
            $judul = "Riwayat Reservasi Kunjungan";

            // Jalankan auto pembatalan kunjungan (hari H atau lewat yang belum dikonfirmasi)
            ReservasiKunjunganService::autoCancelPassedReservations();

            $data = DB::table('reservasi_kunjungan')
                ->leftJoin('users', 'reservasi_kunjungan.id_user', '=', 'users.id')
                ->leftJoin('kunjungans', 'reservasi_kunjungan.id_kunjungan', '=', 'kunjungans.id')
                ->where('reservasi_kunjungan.id_user', '=', $userId)
                ->select('reservasi_kunjungan.*', 'kunjungans.name as paket_name')
                ->orderBy('reservasi_kunjungan.created_at', 'desc')
                ->paginate($perPage);

            foreach ($data as $k) {
                // Jalankan cek expired / batalkan per item
                $newStatus = ReservasiKunjunganService::expireIfNeeded((int) $k->id_reservasi);
                if ($newStatus) {
                    $k->status_pembayaran = $newStatus;
                }

                // 1. Cek Selesai Berdasarkan Tanggal
                if (Carbon::parse($k->tanggal_reservasi)->startOfDay() < Carbon::today() && ($k->status_pembayaran == 'Lunas' || $k->status_pembayaran == 'Diterima')) {
                    DB::table('reservasi_kunjungan')
                        ->where('id_reservasi', '=', $k->id_reservasi, 'and')
                        ->update(['status_pembayaran' => 'Selesai', 'updated_at' => now()]);
                    $k->status_pembayaran = 'Selesai';
                }

                // 2. Cek Expired QRIS
                if ($k->status_pembayaran == 'Menunggu Pembayaran' && $k->expires_at && Carbon::parse($k->expires_at)->isPast()) {
                    DB::table('reservasi_kunjungan')
                        ->where('id_reservasi', '=', $k->id_reservasi, 'and')
                        ->update(['status_pembayaran' => 'Expired', 'updated_at' => now()]);
                    $k->status_pembayaran = 'Expired';
                }
            }

            $backRoute = 'nazfram.kunjungan';
            return view('riwayat', compact('judul', 'data', 'backRoute'));
        }
    }

    public function riwayatMagang(Request $request)
    {
        $request->merge(['type' => 'magang']);
        return $this->riwayat($request);
    }

    public function formPendaftaran($id)
    {
        $magang = Magang::findOrFail($id);
        return view('pendaftaran_pelatihan', [
            'id_pelatihan' => $id,
            'magang' => $magang
        ]);
    }

    public function store(Request $request)
    {
        \Log::info('Store Magang Hit', [
            'user_id' => \Auth::id(),
            'role' => \Auth::user() ? \Auth::user()->role : 'guest',
            'id_pelatihan' => $request->id_pelatihan
        ]);
        $request->validate([
            'id_pelatihan' => 'required|exists:magangs,id',
        ]);

        $magangPkg = Magang::findOrFail($request->id_pelatihan);

        $rules = [
            'tanggal_magang' => 'required|date|after_or_equal:' . now()->addDays(3)->format('Y-m-d'),
            'jumlah_peserta' => 'required|integer|min:1',
            'instansi' => 'required|string|max:255',
            'metode_pembayaran' => 'nullable|string',
            'total_harga' => 'required|numeric',
        ];

        if ($magangPkg->show_skill_description) {
            $rules['deskripsi_kemampuan'] = 'required|string';
        }

        $request->validate($rules, [
            'deskripsi_kemampuan.required' => 'Deskripsi kemampuan wajib diisi untuk program ini.',
        ]);
        $total_harga = $magangPkg->price * $request->jumlah_peserta;

        // Tentukan status dan metode pembayaran berdasarkan is_wa_confirmation
        $status_pembayaran = 'Menunggu Konfirmasi';
        $metode_pembayaran = 'pending';
        $expires_at = null;

        if ($magangPkg->is_wa_confirmation) {
            // Dengan konfirmasi WA: tunggu konfirmasi admin dulu
            $metode_pembayaran = 'konfirmasi_wa';
            $status_pembayaran = 'Menunggu Konfirmasi';
        } else {
            // Tanpa konfirmasi WA
            if ($total_harga > 0) {
                // Berbayar: langsung ke pembayaran
                $metode_pembayaran = 'qris';
                $status_pembayaran = 'Menunggu Pembayaran';
                $expires_at = now()->addMinutes(PendaftaranMagangService::PAYMENT_MINUTES);
            } else {
                // Gratis: tunggu konfirmasi admin
                $metode_pembayaran = 'gratis';
                $status_pembayaran = 'Menunggu Konfirmasi';
            }
        }

        $tanggal_magang = \Carbon\Carbon::parse($request->tanggal_magang)->toDateString();

        // Logika Peserta Kolektif
        $isPendaftarIkut = $request->input('is_pendaftar_ikut', 1);
        $listNamaRaw = $request->input('list_nama_peserta', []);
        $listNama = array_filter($listNamaRaw, fn($name) => !empty(trim($name)));
        $listNamaJson = !empty($listNama) ? json_encode(array_values($listNama)) : null;

        $id_pendaftaran = DB::table('pendaftaran_magang')->insertGetId([
            'id_user' => Auth::id(),
            'id_magang' => $request->id_pelatihan,
            'paket_name' => $magangPkg->name,
            'tanggal_magang' => $tanggal_magang,
            'durasi_magang' => $request->jumlah_peserta,
            'pekerjaan' => $request->instansi,
            'deskripsi_kemampuan' => $request->deskripsi_kemampuan,
            'is_pendaftar_ikut' => $isPendaftarIkut,
            'list_nama_peserta' => $listNamaJson,
            'total_harga' => $total_harga,
            'metode_pembayaran' => $metode_pembayaran,
            'status_pembayaran' => $status_pembayaran,
            'expires_at' => $expires_at,
            'created_at' => now(),
            'midtrans_order_id' => null,
        ]);

        // Jika butuh konfirmasi WA (baik berbayar maupun gratis)
        if ($magangPkg->is_wa_confirmation) {
            $adminPhone = \App\Models\Setting::get('whatsapp_admin', '6282240867746');
            $adminPhone = preg_replace('/[^0-9]/', '', $adminPhone);
            if (str_starts_with($adminPhone, '0')) {
                $adminPhone = '62' . substr($adminPhone, 1);
            }
            $pesan = "Assalamu'alaikum Warahmatullahi Wabarakatuh,\n\n";
            $pesan .= "Halo Admin Naz Hidrofarm,\n\n";
            $pesan .= "Perkenalkan, saya mewakili pendaftaran Magang PKL:\n\n";
            $pesan .= "Pendaftar    : " . Auth::user()->name . ($isPendaftarIkut ? " (Ikut Melakukan Magang)" : " (Perwakilan dari sekolah/kampus)") . "\n";
            
            if (!empty($listNama)) {
                $pesan .= "Daftar Siswa : \n";
                foreach ($listNama as $idx => $namaSiswa) {
                    $pesan .= ($idx + 1) . ". " . $namaSiswa . "\n";
                }
            }

            $pesan .= "\nID Daftar    : #MAG-" . str_pad($id_pendaftaran, 5, '0', STR_PAD_LEFT) . "\n";
            $pesan .= "Paket        : " . $magangPkg->name . "\n";
            $pesan .= "Tgl Mulai    : " . \Carbon\Carbon::parse($tanggal_magang)->locale('id')->translatedFormat('d F Y') . "\n";
            $pesan .= "Durasi       : " . $request->jumlah_peserta . " Bulan\n\n";
            $pesan .= "Kami ingin mengonfirmasi pendaftaran tersebut di Naz Hidrofarm dan menyatakan kesiapan untuk mengikuti program sesuai jadwal yang telah ditentukan.\n\n";
            $pesan .= "Mohon pendaftaran kami dapat segera diproses. Atas perhatiannya, kami ucapkan terima kasih.\n\n";
            $pesan .= "Wassalamu'alaikum Warahmatullahi Wabarakatuh.";

            $waUrl = "https://wa.me/" . $adminPhone . "?text=" . urlencode($pesan);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'wa_link' => $waUrl,
                    'redirect_url' => route('magang.riwayat')
                ]);
            }

            return redirect()->route('magang.riwayat')->with([
                'success' => 'Pendaftaran berhasil dikirim!',
                'wa_link' => $waUrl
            ]);
        }

        // Jika status Menunggu Pembayaran (non-WA confirmation, berbayar), langsung ke pembayaran
        if ($status_pembayaran === 'Menunggu Pembayaran') {
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'redirect_url' => route('nazfram.pembayaran_magang', $id_pendaftaran)
                ]);
            }

            return redirect()->route('nazfram.pembayaran_magang', $id_pendaftaran);
        }

        // Untuk kasus lainnya (gratis non-WA confirmation), ke riwayat
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'redirect_url' => route('magang.riwayat')
            ]);
        }

        return redirect()->route('magang.riwayat')->with('success', 'Pendaftaran berhasil dikirim!');
    }

    public function keranjang()
    {
        $this->checkAndRestoreExpiredOrders(); // Cek jika ada pesanan qris yang expired
        CartItem::clearExpiredCarts(); // Bersihkan keranjang yang sudah lebih dari 2 hari

        $cart = $this->getOrCreateCart();
        $cartItems = CartItem::where("cart_id", "=", $cart->id)->with("product")->get();
        $items = [];
        $total = 0;

        foreach ($cartItems as $ci) {
            if (!$ci->product)
                continue;
            $subtotal = $ci->quantity * $ci->product->price;
            $items[] = [
                "cart_item_id" => $ci->id,
                "nama" => $ci->product->name,
                "harga" => $ci->product->price,
                "jumlah" => $ci->quantity,
                "subtotal" => $subtotal,
                "stok" => $ci->product->stock,
                "gambar" => $ci->product->image_url,
            ];
            $total += $subtotal;
        }
        return view("keranjang", ["items" => $items, "total" => $total]);
    }

    public function tambahKeKeranjang(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:products,id',
            'jumlah' => 'required|integer|min:1'
        ]);

        $product = Product::find($request->id);

        if ($product->stock < $request->jumlah) {
            return response()->json(['message' => 'Stok tidak cukup'], 400);
        }

        $cart = $this->getOrCreateCart();

        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', '=', $product->id, 'and')
            ->first();

        if ($cartItem) {
            $cartItem->quantity += $request->jumlah;
            $cartItem->save();
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $request->jumlah
            ]);
        }

        // Kurangi stok
        $product->stock -= $request->jumlah;
        $product->save();

        $cartCount = CartItem::where("cart_id", $cart->id)->sum('quantity');

        return response()->json([
            'success' => true,
            'message' => 'Berhasil ditambahkan ke keranjang',
            'cartCount' => $cartCount,
            'updated_stock' => $product->stock
        ]);
    }

    public function getPublicEvents(Request $request)
    {
        $startLimit = Carbon::parse($request->input('start'))->startOfDay();
        $endLimit = Carbon::parse($request->input('end'))->endOfDay();

        $events = [];

        // 1. Ambil hari libur (TUTUP)
        $jadwals = Jadwal::where('kategori', 'libur')
            ->where('start_date', '<=', $endLimit, 'and')
            ->where('end_date', '>=', $startLimit, 'and')
            ->get();

        foreach ($jadwals as $j) {
            $events[] = [
                'id' => "libur-{$j->id}",
                'title' => 'TUTUP',
                'start' => Carbon::parse($j->start_date)->format('Y-m-d'),
                'end' => $j->end_date ? Carbon::parse($j->end_date)->addDay()->format('Y-m-d') : null,
                'display' => 'background',
                'color' => '#ffcccc', // Merah muda untuk libur
            ];
        }

        // 2. Ambil pendaftaran kunjungan yang sudah ada (SOLD OUT) - Hanya jika type adalah kunjungan
        if ($request->input('type') == 'kunjungan') {
            $kunjungans = DB::table('reservasi_kunjungan')
                ->where('tanggal_reservasi', '>=', $startLimit->format('Y-m-d'), 'and')
                ->where('tanggal_reservasi', '<=', $endLimit->format('Y-m-d'), 'and')
                ->whereNotIn('status_pembayaran', ['Dibatalkan', 'Tidak Diterima', 'Expired', 'Dibatalkan Pengguna'])
                ->get();

            foreach ($kunjungans as $k) {
                $events[] = [
                    'id' => "sold-{$k->id_reservasi}",
                    'title' => 'SOLD OUT',
                    'start' => $k->tanggal_reservasi,
                    'display' => 'background',
                    'color' => '#28a745', // Hijau solid untuk sold out (Kunjungan)
                ];
            }
        }

        return response()->json($events);
    }

    public function updateKeranjang(Request $request)
    {
        $request->validate([
            'cart_item_id' => 'required|exists:cart_items,id',
            'action' => 'required|in:increment,decrement'
        ]);

        $cartItem = CartItem::find($request->cart_item_id);
        $product = Product::find($cartItem->product_id);

        if ($request->action == 'increment') {
            if ($product->stock > 0) {
                $cartItem->quantity++;
                $product->stock--;
                $cartItem->save();
                $product->save();
            } else {
                return response()->json(['success' => false, 'message' => 'Stok tidak cukup']);
            }
        } elseif ($request->action == 'decrement') {
            if ($cartItem->quantity > 1) {
                $cartItem->quantity--;
                $product->stock++;
                $cartItem->save();
                $product->save();
            } else {
                $product->stock++;
                $product->save();
                $cartItem->delete();
                return response()->json(['success' => true, 'removed' => true]);
            }
        }

        return response()->json([
            'success' => true,
            'quantity' => $cartItem->quantity,
            'stok' => $product->stock,
            'subtotal' => $cartItem->quantity * $product->price
        ]);
    }

    public function hapusKeranjang(Request $request)
    {
        $request->validate([
            'cart_item_id' => 'required|exists:cart_items,id'
        ]);

        $cartItem = CartItem::find($request->cart_item_id);

        // Kembalikan stok
        $product = Product::find($cartItem->product_id);
        if ($product) {
            $product->stock += $cartItem->quantity;
            $product->save();
        }

        $cartItem->delete();

        return response()->json(['success' => true, 'message' => 'Item dihapus dari keranjang']);
    }

    public function pesanan(Request $request)
    {
        $selectedItems = $request->input('selected_items', []);

        if (empty($selectedItems)) {
            return redirect()->route('nazfram.keranjang')->with('error', 'Silakan pilih produk yang ingin dipesan terlebih dahulu.');
        }

        $cart = $this->getOrCreateCart();
        $cartItems = CartItem::where("cart_id", $cart->id)
            ->whereIn("id", $selectedItems)
            ->with("product")->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('nazfram.keranjang')->with('error', 'Produk yang dipilih tidak valid.');
        }

        $items = [];
        $totalProduk = 0;
        $melonQty = 0;
        $vegTotal = 0;

        foreach ($cartItems as $ci) {
            if (!$ci->product)
                continue;

            $subtotal = $ci->quantity * $ci->product->price;
            $items[] = [
                "nama" => $ci->product->name,
                "harga" => $ci->product->price,
                "jumlah" => $ci->quantity,
                "subtotal" => $subtotal,
                "gambar" => $ci->product->image_url,
            ];
            $totalProduk += $subtotal;

            // Logika Syarat Pengantaran
            if (stripos($ci->product->name, 'Melon') !== false) {
                $melonQty += $ci->quantity;
            }
            if (stripos($ci->product->name, 'Pakcoy') !== false || stripos($ci->product->name, 'Selada') !== false) {
                $vegTotal += $subtotal;
            }
        }

        // Syarat: Minimal 5kg Melon ATAU Total Pakcoy+Selada minimal Rp 150.000
        $canDeliver = ($melonQty >= 5) || ($vegTotal >= 150000);

        return view("pesanan", [
            "items" => $items,
            "totalProduk" => $totalProduk,
            "canDeliver" => $canDeliver,
            "melonQty" => $melonQty,
            "vegTotal" => $vegTotal,
            "user" => Auth::user(),
            "selectedItems" => $selectedItems
        ]);
    }
    public function prosesPesanan(Request $request)
    {
        $request->validate([
            'metode_pengiriman' => 'required|in:pengambilan,pengantaran',
            'metode_pembayaran' => 'required|in:qris',
            'alamat' => 'required|string',
            'jarak' => 'nullable|numeric',
            'total_produk' => 'required|numeric',
            'ongkir' => 'required|numeric',
            'grand_total' => 'required|numeric',
            'selected_items' => 'required|array',
            'selected_items.*' => 'exists:cart_items,id'
        ]);

        $cart = $this->getOrCreateCart();
        $cartItems = CartItem::where("cart_id", $cart->id)
            ->whereIn("id", $request->selected_items)
            ->with("product")->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('nazfram.produk')->with('error', 'Keranjang belanja Anda kosong atau produk tidak valid.');
        }

        // VALIDASI PENGANTARAN
        if ($request->metode_pengiriman == 'pengantaran') {
            $melonQty = 0;
            $vegTotal = 0;

            foreach ($cartItems as $ci) {
                if (stripos($ci->product->name, 'Melon') !== false)
                    $melonQty += $ci->quantity;

                if (stripos($ci->product->name, 'Pakcoy') !== false || stripos($ci->product->name, 'Selada') !== false) {
                    $vegTotal += ($ci->quantity * $ci->product->price);
                }
            }

            if ($melonQty < 5 && $vegTotal < 150000) {
                return back()->with('error', 'Pesanan tidak memenuhi syarat pengantaran.');
            }
        }

        // =========================
        // 1. SIMPAN ORDER
        // =========================
        $order = Order::create([
            'user_id' => Auth::id(),
            'total_produk' => $request->total_produk,
            'ongkir' => $request->ongkir,
            'grand_total' => $request->grand_total,
            'metode_pembayaran' => $request->metode_pembayaran,
            'metode_pengiriman' => $request->metode_pengiriman,
            'alamat' => $request->alamat,
            'jarak' => $request->jarak,
            'status' => 'Menunggu Pembayaran',
            'expires_at' => $request->metode_pembayaran == 'qris'
                ? now()->addMinutes(30)
                : null,
        ]);

        // =========================
        // 2. SIMPAN ITEM
        // =========================
        foreach ($cartItems as $ci) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $ci->product_id,
                'product_name' => $ci->product->name,
                'quantity' => $ci->quantity,
                'price' => $ci->product->price
            ]);
        }

        // =========================
        // 4. Generate Order ID
        // =========================
        $orderId = 'ORD-' . $order->id . '-' . time();

        $qrUrl = \App\Models\Setting::get('qris_image', '');

        $order->update([
            'midtrans_order_id' => $orderId,
            'qr_url' => $qrUrl
        ]);

        // HAPUS CART setelah order terbuat
        CartItem::where("cart_id", $cart->id)->whereIn("id", $request->selected_items)->delete();

        // Simpan qrUrl ke session dulu
        session(['qrUrl' => $qrUrl]);

        return redirect()->route('nazfram.pembayaran', $order->id); // ✅ REDIRECT
    }


    private function checkAndRestoreExpiredOrders()
    {
        $expiredOrders = Order::where('status', 'Menunggu Pembayaran')
            ->where('metode_pembayaran', '=', 'qris', 'and')
            ->where('expires_at', '<', now(), 'and')
            ->get();

        foreach ($expiredOrders as $order) {
            $this->restoreToCart($order, 'Expired');
        }
    }

    private function restoreToCart($order, $status)
    {
        $cart = $this->getOrCreateCart();
        $orderItems = OrderItem::where('order_id', $order->id)->get();

        foreach ($orderItems as $item) {
            // Cek jika item sudah ada di keranjang
            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('product_id', '=', $item->product_id, 'and')
                ->first();

            if ($cartItem) {
                $cartItem->update([
                    'quantity' => $cartItem->quantity + $item->quantity
                ]);
            } else {
                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity
                ]);
            }
        }

        // Update status order atau hapus? User bilang "kembali ke keranjang pengguna bukan menghilang"
        // Kita ubah statusnya jadi 'Expired' atau 'Dibatalkan Pengguna'
        $order->status = $status;
        $order->save();
    }


    public function riwayatPesanan(Request $request)
    {
        $perPage = $request->has('all') ? 1000 : 8;
        $orders = Order::where('user_id', Auth::id())
            ->with(['items.product'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        foreach ($orders as $order) {
            if ($order->status == 'Menunggu Pembayaran' && $order->expires_at && Carbon::parse($order->expires_at)->isPast()) {
                $this->restoreToCart($order, 'Expired');
            }
        }

        return view('riwayat_pesanan', compact('orders'));
    }

    public function expirePesanan($id)
    {
        $order = Order::findOrFail($id);
        if ($order->user_id == Auth::id() && $order->status == 'Menunggu Pembayaran') {
            $this->restoreToCart($order, 'Dibatalkan Pengguna');
        }
        return response()->json(['success' => true]);
    }
}
