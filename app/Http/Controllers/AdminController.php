<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Item;
use App\Models\User;
use App\Models\Product;
use App\Models\Kunjungan;
use App\Models\Magang;
use App\Models\Jadwal;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CartItem;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Mail\SendKodeVerifikasiProfil;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Hapus dulu middleware session manual di sini untuk testing
    }

    public function admin()
    {
        if (!$this->isAdmin()) {
            return redirect()
                ->route("login")
                ->with("error", "Silakan login sebagai admin.");
        }

        $today = Carbon::today();

        // 1. Pesanan Hari Ini
        $pesananHariIniCount = Order::whereDate('created_at', $today)
            ->whereNotIn('status', ['Dibatalkan Pengguna', 'Expired'])
            ->count();

        // 2. Kunjungan 3 Hari Kedepan (Pelaksanaan)
        $threeDaysLater = Carbon::today()->addDays(3);
        $detailKunjunganHariIni = DB::table('reservasi_kunjungan')
            ->join('users', 'reservasi_kunjungan.id_user', '=', 'users.id')
            ->join('kunjungans', 'reservasi_kunjungan.id_kunjungan', '=', 'kunjungans.id')
            ->whereBetween('tanggal_reservasi', [$today->format('Y-m-d'), $threeDaysLater->format('Y-m-d')])
            ->whereNotIn('status_pembayaran', ['Dibatalkan Pengguna', 'Tidak Diterima', 'Expired', 'Dibatalkan'])
            ->select('users.name as user_name', 'kunjungans.name as paket_name', 'reservasi_kunjungan.tanggal_reservasi', 'reservasi_kunjungan.instansi', 'reservasi_kunjungan.jumlah_peserta')
            ->orderBy('tanggal_reservasi', 'asc')
            ->get();
        $kunjunganHariIniCount = $detailKunjunganHariIni->count();

        // 3. Magang Aktif Hari Ini (Sedang berjalan)
        $detailMagangAktif = DB::table('pendaftaran_magang')
            ->join('users', 'pendaftaran_magang.id_user', '=', 'users.id')
            ->join('magangs', 'pendaftaran_magang.id_magang', '=', 'magangs.id')
            ->select('users.name as user_name', 'magangs.name as paket_name', 'pendaftaran_magang.tanggal_magang', 'pendaftaran_magang.durasi_magang')
            ->get()
            ->filter(function ($m) use ($today) {
                $start = Carbon::parse($m->tanggal_magang)->startOfDay();
                $end = Carbon::parse($m->tanggal_magang)->addMonths($m->durasi_magang)->endOfDay();
                return $today->between($start, $end);
            })
            ->sortBy(function ($m) {
                return Carbon::parse($m->tanggal_magang)->addMonths($m->durasi_magang);
            });
        $magangAktifCount = $detailMagangAktif->count();

        // 4. Pesanan Hari Ini (Realtime)
        $recentOrders = Order::join('users', 'orders.user_id', '=', 'users.id')
            ->whereDate('orders.created_at', $today)
            ->whereNotIn('orders.status', ['Dibatalkan Pengguna', 'Expired', 'Dibatalkan'])
            ->select('orders.*', 'users.name as user_name')
            ->orderBy('orders.created_at', 'desc')
            ->take(15)
            ->get();

        // 5. Magang akan Mulai 3 Hari Kedepan
        $threeDaysLater = Carbon::today()->addDays(3);
        $detailMagangMulai = DB::table('pendaftaran_magang')
            ->join('users', 'pendaftaran_magang.id_user', '=', 'users.id')
            ->join('magangs', 'pendaftaran_magang.id_magang', '=', 'magangs.id')
            ->whereBetween('tanggal_magang', [$today->format('Y-m-d'), $threeDaysLater->format('Y-m-d')])
            ->whereNotIn('status_pembayaran', ['Dibatalkan Pengguna', 'Tidak Diterima', 'Expired', 'Dibatalkan'])
            ->select('users.name as user_name', 'magangs.name as paket_name', 'pendaftaran_magang.tanggal_magang', 'pendaftaran_magang.pekerjaan as instansi')
            ->orderBy('tanggal_magang', 'asc')
            ->get();

        // 6. Counter Pendaftaran Baru Hari Ini (Tetap ada)
        $daftarMagangHariIni = DB::table('pendaftaran_magang')->whereDate('created_at', $today)
            ->whereNotIn('status_pembayaran', ['Dibatalkan Pengguna', 'Expired', 'Dibatalkan'])->count();
        $daftarKunjunganHariIni = DB::table('reservasi_kunjungan')->whereDate('created_at', $today)
            ->whereNotIn('status_pembayaran', ['Dibatalkan Pengguna', 'Expired', 'Dibatalkan'])->count();

        return view("admin.dashboard", [
            'pesananHariIniCount' => $pesananHariIniCount,
            'kunjunganHariIniCount' => $kunjunganHariIniCount,
            'magangAktifCount' => $magangAktifCount,
            'detailKunjunganHariIni' => $detailKunjunganHariIni,
            'detailMagangAktif' => $detailMagangAktif,
            'daftarMagangHariIni' => $daftarMagangHariIni,
            'daftarKunjunganHariIni' => $daftarKunjunganHariIni,
            'recentOrders' => $recentOrders,
            'detailMagangMulai' => $detailMagangMulai,
            'todayStr' => $today->format('Y-m-d')
        ]);
    }

    public function transaksiAdmin(Request $request)
    {
        if (!$this->isAdmin()) {
            return redirect()->route("login")->with("error", "Akses ditolak.");
        }

        $status = $request->query('status');
        $search = $request->query('search');
        $date = $request->query('date');

        $orders = Order::with('user')
            ->whereNotIn('status', ['Expired', 'Dibatalkan Pengguna'])
            ->when($status, function ($query, $status) {
                if ($status == 'offline') {
                    return $query->where('is_offline', 1);
                }
                return $query->where('status', $status);
            })
            ->when($status != 'offline', function ($query) use ($status) {
                if ($status == 'Selesai') {
                    return $query;
                }
                return $query->where('is_offline', 0);
            })
            ->when($date, function ($query, $date) {
                return $query->whereDate('created_at', $date);
            })
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    // 1. Pencarian ID (Hapus prefiks ORD- atau # jika ada, lalu ambil angka)
                    $cleanSearch = str_replace(['#', 'ORD-', 'ord-'], '', $search);
                    $numericSearch = preg_replace('/[^0-9]/', '', $cleanSearch);

                    $q->where('id', 'LIKE', "%{$search}%")
                      ->orWhere('status', 'LIKE', "%{$search}%")
                      ->orWhereHas('user', function ($qu) use ($search) {
                          $qu->where('name', 'LIKE', "%{$search}%");
                      });

                    if ($numericSearch !== '') {
                        $q->orWhere('id', (int)$numericSearch);
                    }

                    // 2. Pencarian Tanggal (Format YYYY-MM-DD atau DD-MM-YYYY)
                    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $search)) {
                        $q->orWhereDate('created_at', $search);
                    } elseif (preg_match('/^\d{2}-\d{2}-\d{4}$/', $search)) {
                        $dateFormatted = \Carbon\Carbon::createFromFormat('d-m-Y', $search)->format('Y-m-d');
                        $q->orWhereDate('created_at', $dateFormatted);
                    } elseif (preg_match('/^\d{1,2}$/', $search)) {
                        // Tambahan: Cari berdasarkan tanggal hari (1-31)
                        $q->orWhereDay('created_at', $search);
                    }
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view("admin.transaksi", compact("orders", "status", "search", "date"));
    }

    public function getDetailTransaksi($id)
    {
        if (!$this->isAdmin()) {
            return response()->json(["success" => false, "message" => "Akses ditolak"], 403);
        }
        $order = Order::with(['user', 'items.product'])->findOrFail($id);
        return response()->json($order);
    }

    public function updateStatusTransaksi(Request $request, $id)
    {
        if (!$this->isAdmin()) {
            return response()->json(["success" => false, "message" => "Akses ditolak"], 403);
        }
        $request->validate([
            'status' => 'required|string'
        ]);

        $order = Order::findOrFail($id);
        $order->status = $request->status;
        $order->save();

        return response()->json(["success" => true, "message" => "Status pesanan berhasil diperbarui"]);
    }

    /**
     * Menampilkan daftar semua user (kecuali admin)
     */
    public function daftarUser(Request $request)
    {
        if (!$this->isAdmin()) {
            return redirect()->route("login")->with("error", "Akses ditolak.");
        }

        $filter = $request->input('filter', 'online');
        if (!in_array($filter, ['online', 'offline'], true)) {
            $filter = 'online';
        }

        $query = User::where("role", "!=", "admin");

        if ($filter === 'offline') {
            $query->where('username', 'like', 'offline_%');
        } else {
            $query->where(function ($q) {
                $q->where('username', 'not like', 'offline_%')
                  ->orWhereNull('username');
            });
        }

        $users = $query->get();

        return view("admin.daftar_user", compact("users", "filter"));
    }

    /**
     * Menampilkan halaman kelola admin (CRUD)
     */
    public function kelolaAdmin()
    {
        if (!$this->isAdmin()) {
            return redirect()->route("login")->with("error", "Akses ditolak.");
        }

        $admins = User::where("role", "admin")->get();
        return view("admin.kelola_admin", ["admins" => $admins]);
    }

    public function createAdmin()
    {
        if (!$this->isAdmin()) {
            return redirect()->route("login")->with("error", "Akses ditolak.");
        }
        return view("admin.kelola_admin_form");
    }

    public function editAdminForm($id)
    {
        if (!$this->isAdmin()) {
            return redirect()->route("login")->with("error", "Akses ditolak.");
        }
        $admin = User::findOrFail($id);
        return view("admin.kelola_admin_form", compact("admin"));
    }

    /**
     * Tambah admin baru
     */
    public function addAdmin(Request $request)
    {
        if (!$this->isAdmin()) {
            return response()->json(["success" => false, "message" => "Akses ditolak"], 403);
        }

        $validated = $request->validate([
            "name" => "required|string|max:255",
            "username" => "required|string|unique:users,username",
            "email" => "required|string|email|unique:users,email",
            "password" => [
                'required',
                'string',
                'min:7',
                'confirmed',
                function ($attribute, $value, $fail) {
                    if (!preg_match('/[a-z]/', $value)) {
                        $fail('Password harus mengandung huruf kecil.');
                    }
                    if (!preg_match('/[A-Z]/', $value)) {
                        $fail('Password harus mengandung huruf besar.');
                    }
                    if (!preg_match('/[0-9]/', $value)) {
                        $fail('Password harus mengandung angka.');
                    }
                    if (!preg_match('/[!@#$%^&*]/', $value)) {
                        $fail('Password harus mengandung simbol (!@#$%^&*).');
                    }
                }
            ],
        ], [
            "password.min" => "Password minimal 7 karakter.",
        ]);

        $validated["nohp"] = "-";
        $validated["alamat"] = "-";
        $validated["password"] = bcrypt($request->password);
        $validated["role"] = "admin";
        $validated["status"] = "active";

        User::create($validated);

        return redirect()->route('admin.kelola-admin')->with('success', 'Admin baru berhasil ditambahkan');
    }

    public function editAdmin(Request $request, $id)
    {
        if (!$this->isAdmin()) {
            return response()->json(["success" => false, "message" => "Akses ditolak"], 403);
        }

        $admin = User::findOrFail($id);

        $validated = $request->validate([
            "name" => "required|string|max:255",
            "username" => "required|string|unique:users,username," . $id,
            "email" => "required|string|email|unique:users,email," . $id,
            "password" => [
                'nullable',
                'string',
                'min:7',
                'confirmed',
                function ($attribute, $value, $fail) {
                    if ($value && !preg_match('/[a-z]/', $value)) {
                        $fail('Password harus mengandung huruf kecil.');
                    }
                    if ($value && !preg_match('/[A-Z]/', $value)) {
                        $fail('Password harus mengandung huruf besar.');
                    }
                    if ($value && !preg_match('/[0-9]/', $value)) {
                        $fail('Password harus mengandung angka.');
                    }
                    if ($value && !preg_match('/[!@#$%^&*]/', $value)) {
                        $fail('Password harus mengandung simbol (!@#$%^&*).');
                    }
                }
            ],
        ], [
            "password.min" => "Password minimal 7 karakter.",
        ]);

        // Cek apakah ada perubahan pada data sensitif (email atau password)
        $emailChanged = $request->email !== $admin->email;
        $passwordChanged = $request->filled("password");
        $hasSensitiveChange = $emailChanged || $passwordChanged;

        // Jika ada perubahan sensitif, pastikan OTP sudah pernah diverifikasi di sesi ini
        if ($hasSensitiveChange && !session('admin_otp_verified')) {
            return redirect()->back()
                ->withErrors(['verification' => 'Untuk mengubah Email atau Password, silakan klik tombol "Verifikasi Email" terlebih dahulu dan masukkan kode yang dikirim ke email admin.'])
                ->withInput();
        }

        // Hanya update password jika diisi
        if ($passwordChanged) {
            $validated["password"] = bcrypt($request->password);
        } else {
            unset($validated["password"]);
        }

        $admin->update($validated);

        // Bersihkan session OTP setelah berhasil update (jika ada)
        session()->forget(['admin_otp_verified', 'admin_otp_target_id', 'admin_otp_code', 'admin_otp_expires']);

        return redirect()->route('admin.kelola-admin')->with('success', 'Data admin berhasil diperbarui!');
    }

    public function kirimKodeAdmin(Request $request)
    {
        $id = $request->id;
        $admin = User::findOrFail($id);
        $email = $admin->email;

        $code = rand(100000, 999999);

        session([
            'admin_otp_code' => $code,
            'admin_otp_target_id' => $id,
            'admin_otp_expires' => Carbon::now()->addMinutes(15)->toDateTimeString(),
        ]);

        try {
            Mail::to($email)->send(new SendKodeVerifikasiProfil($code, 'pengaturan akun'));
            return response()->json(['success' => true, 'message' => 'Kode verifikasi telah dikirim ke email admin tersebut.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengirim email. Pastikan koneksi internet stabil.'], 500);
        }
    }

    public function verifikasiKodeAdmin(Request $request)
    {
        $request->validate([
            'kode' => 'required|numeric',
            'id' => 'required'
        ]);

        $storedCode = session('admin_otp_code');
        $storedTarget = session('admin_otp_target_id');
        $storedExpiry = session('admin_otp_expires');

        if (!$storedCode || !$storedExpiry || $storedTarget != $request->id) {
            return response()->json(['success' => false, 'message' => 'Sesi verifikasi tidak valid atau sudah kedaluwarsa.'], 400);
        }

        if (Carbon::now()->gt(Carbon::parse($storedExpiry))) {
            return response()->json(['success' => false, 'message' => 'Kode sudah kedaluwarsa.'], 400);
        }

        if ((string) $storedCode !== (string) $request->kode) {
            return response()->json(['success' => false, 'message' => 'Kode verifikasi salah.'], 400);
        }

        session(['admin_otp_verified' => true]);

        return response()->json(['success' => true, 'message' => 'Kode berhasil diverifikasi!']);
    }

    public function ubahEmailAdmin(Request $request)
    {
        $id = $request->id;
        $request->validate([
            'email_baru' => 'required|email|unique:users,email,' . $id,
        ], [
            'email_baru.unique' => 'Email sudah digunakan oleh admin lain.'
        ]);

        if (!session('admin_otp_verified') || session('admin_otp_target_id') != $id) {
            return response()->json(['success' => false, 'message' => 'Sesi verifikasi tidak valid.'], 403);
        }

        $admin = User::findOrFail($id);
        $admin->email = $request->email_baru;
        $admin->save();

        session()->forget(['admin_otp_verified', 'admin_otp_target_id', 'admin_otp_code', 'admin_otp_expires']);

        return response()->json(['success' => true, 'message' => 'Email admin berhasil diubah!']);
    }

    public function ubahPasswordAdmin(Request $request)
    {
        $id = $request->id;
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
            'password_baru.min' => 'Password minimal 7 karakter.',
        ]);

        if (!session('admin_otp_verified') || session('admin_otp_target_id') != $id) {
            return response()->json(['success' => false, 'message' => 'Sesi verifikasi tidak valid.'], 403);
        }

        $admin = User::findOrFail($id);
        $admin->password = Hash::make($request->password_baru);
        $admin->save();

        session()->forget(['admin_otp_verified', 'admin_otp_target_id', 'admin_otp_code', 'admin_otp_expires']);

        return response()->json(['success' => true, 'message' => 'Password admin berhasil diubah!']);
    }

    /**
     * Hapus admin
     */
    public function deleteAdmin($id)
    {
        if (!$this->isAdmin()) {
            return response()->json(
                ["success" => false, "message" => "Akses ditolak"],
                403,
            );
        }

        $admin = User::findOrFail($id);
        $admin->delete();

        return response()->json([
            "success" => true,
            "message" => "Admin berhasil dihapus",
        ]);
    }

    /**
     * Mendapatkan detail admin untuk edit
     */
    public function getAdmin($id)
    {
        if (!$this->isAdmin()) {
            return response()->json(
                ["success" => false, "message" => "Akses ditolak"],
                403,
            );
        }

        $admin = User::findOrFail($id);
        return response()->json($admin);
    }

    /**
     * Helper method untuk mengecek apakah user adalah admin
     */
    private function isAdmin()
    {
        // Cek dari session terlebih dahulu
        if (session()->get("is_admin")) {
            return true;
        }

        // Cek dari authenticated user di database
        if (auth()->check() && auth()->user()->role === "admin") {
            return true;
        }

        return false;
    }


    public function produkAdmin()
    {
        if (!$this->isAdmin()) {
            return redirect()->route("login")->with("error", "Akses ditolak.");
        }
        CartItem::clearExpiredCarts();
        $products = Product::all();
        foreach ($products as $product) {
            $product->cart_stock = CartItem::where('product_id', $product->id)->sum('quantity');
        }
        return view("admin.produk", compact("products"));
    }

    public function createProduct()
    {
        if (!$this->isAdmin()) {
            return redirect()->route("login")->with("error", "Akses ditolak.");
        }
        return view("admin.produk_form");
    }

    public function editProductForm($id)
    {
        if (!$this->isAdmin()) {
            return redirect()->route("login")->with("error", "Akses ditolak.");
        }
        $product = Product::findOrFail($id);
        $product->cart_stock = CartItem::where('product_id', $product->id)->sum('quantity');
        return view("admin.produk_form", compact("product"));
    }

    /**
     * Tambah produk baru
     */
    public function addProduct(Request $request)
    {
        if (!$this->isAdmin()) {
            return redirect()->route("login")->with("error", "Akses ditolak.");
        }

        $validated = $request->validate([
            "name" => "required|string|unique:products,name",
            "price" => "required|numeric",
            "stock" => "required|integer",
            "description" => "nullable|string",
            "image" => "nullable|image|mimes:jpeg,png,jpg,gif|max:10240",
        ], [
            'name.unique' => 'Nama produk sudah ada, silakan gunakan nama lain.'
        ]);

        if ($request->hasFile("image")) {
            $uploaded = Cloudinary::upload($request->file("image")->getRealPath(), ['folder' => 'products']);
            $validated["image"] = $uploaded->getSecurePath();
        }

        Product::create($validated);

        return redirect()->route('admin.produk-admin')->with('success', 'Produk berhasil ditambahkan');
    }

    /**
     * Edit produk
     */
    public function editProduct(Request $request, $id)
    {
        if (!$this->isAdmin()) {
            return redirect()->route("login")->with("error", "Akses ditolak.");
        }

        $product = Product::findOrFail($id);

        $validated = $request->validate([
            "name" => "required|string|unique:products,name," . $id,
            "price" => "required|numeric",
            "stock" => "required|integer",
            "add_stock" => "nullable|integer|min:0",
            "reduce_stock" => "nullable|integer|min:0",
            "description" => "nullable|string",
            "image" => "nullable|image|mimes:jpeg,png,jpg,gif|max:10240",
        ], [
            'name.unique' => 'Nama produk sudah ada, silakan gunakan nama lain.'
        ]);

        $cart_stock = CartItem::where('product_id', $product->id)->sum('quantity');

        // Hitung total fisik lama
        $current_total_stock = $product->stock + $cart_stock;

        $add = $request->input('add_stock', 0);
        $reduce = $request->input('reduce_stock', 0);

        // Hitung total fisik baru
        $new_total_stock = $current_total_stock + $add - $reduce;

        if ($new_total_stock < 0) {
            return redirect()->back()->withErrors(['stock' => 'Stok akhir tidak boleh kurang dari 0.'])->withInput();
        }

        // Stok tersedia baru yang disimpan ke database
        $finalStock = max(0, $new_total_stock - $cart_stock);

        $validated['stock'] = $finalStock;

        if ($request->hasFile("image")) {
            // Hapus gambar lama dari Cloudinary (jika URL Cloudinary)
            if ($product->image && str_contains($product->image, 'cloudinary')) {
                $publicId = pathinfo(parse_url($product->image, PHP_URL_PATH), PATHINFO_FILENAME);
                Cloudinary::destroy('products/' . $publicId);
            }
            $uploaded = Cloudinary::upload($request->file("image")->getRealPath(), ['folder' => 'products']);
            $validated["image"] = $uploaded->getSecurePath();
        }

        $product->update($validated);

        return redirect()->route('admin.produk-admin')->with('success', 'Produk berhasil diperbarui. Stok sekarang: ' . $finalStock);
    }

    /**
     * Hapus produk
     */
    public function deleteProduct($id)
    {
        if (!$this->isAdmin()) {
            return response()->json(
                ["success" => false, "message" => "Akses ditolak"],
                403,
            );
        }

        $product = Product::findOrFail($id);

        // Hapus gambar dari storage
        if ($product->image) {
            Storage::disk("public")->delete($product->image);
        }

        $product->delete();

        return response()->json([
            "success" => true,
            "message" => "Produk berhasil dihapus",
        ]);
    }

    /**
     * Mendapatkan detail produk untuk edit
     */
    public function getProduct($id)
    {
        if (!$this->isAdmin()) {
            return response()->json(
                ["success" => false, "message" => "Akses ditolak"],
                403,
            );
        }

        CartItem::clearExpiredCarts();
        $product = Product::findOrFail($id);
        $product->cart_stock = CartItem::where('product_id', $product->id)->sum('quantity');
        return response()->json($product);
    }

    public function kunjunganAdmin()
    {
        if (!$this->isAdmin()) {
            return redirect()->route("login")->with("error", "Akses ditolak.");
        }
        $kunjungans = Kunjungan::all();
        return view("admin.kunjungan", compact("kunjungans"));
    }

    public function createKunjungan()
    {
        if (!$this->isAdmin()) {
            return redirect()->route("login")->with("error", "Akses ditolak.");
        }
        return view("admin.kunjungan_form");
    }

    public function editKunjunganForm($id)
    {
        if (!$this->isAdmin()) {
            return redirect()->route("login")->with("error", "Akses ditolak.");
        }
        $kunjungan = Kunjungan::findOrFail($id);
        return view("admin.kunjungan_form", compact("kunjungan"));
    }

    public function addKunjungan(Request $request)
    {
        if (!$this->isAdmin()) {
            return redirect()->route("login")->with("error", "Akses ditolak.");
        }

        $validated = $request->validate([
            "name" => "required|string|unique:kunjungans,name",
            "price" => "required|numeric",
            "min_people" => "required|integer",
            "max_people" => "required|integer",
            "description" => "required|string",
            "image" => "nullable|image|mimes:jpeg,png,jpg,gif|max:10240",
        ], [
            'name.unique' => 'Nama paket kunjungan sudah ada, silakan gunakan nama lain.'
        ]);

        if ($request->hasFile("image")) {
            $uploaded = Cloudinary::upload($request->file("image")->getRealPath(), ['folder' => 'kunjungans']);
            $validated["image"] = $uploaded->getSecurePath();
        }

        Kunjungan::create($validated);

        return redirect()->route('admin.kunjungan-admin')->with('success', 'Kunjungan berhasil ditambahkan');
    }

    public function editKunjungan(Request $request, $id)
    {
        if (!$this->isAdmin()) {
            return response()->json(["success" => false, "message" => "Akses ditolak"], 403);
        }

        $kunjungan = Kunjungan::findOrFail($id);

        $validated = $request->validate([
            "name" => "required|string|unique:kunjungans,name," . $id,
            "price" => "required|numeric",
            "min_people" => "required|integer",
            "max_people" => "required|integer",
            "description" => "required|string",
            "image" => "nullable|image|mimes:jpeg,png,jpg,gif|max:10240",
        ], [
            'name.unique' => 'Nama paket kunjungan sudah ada, silakan gunakan nama lain.'
        ]);

        if ($request->hasFile("image")) {
            $uploaded = Cloudinary::upload($request->file("image")->getRealPath(), ['folder' => 'kunjungans']);
            $validated["image"] = $uploaded->getSecurePath();
        }

        $kunjungan->update($validated);

        return redirect()->route('admin.kunjungan-admin')->with('success', 'Kunjungan berhasil diperbarui');
    }

    public function deleteKunjungan($id)
    {
        if (!$this->isAdmin()) {
            return response()->json(["success" => false, "message" => "Akses ditolak"], 403);
        }

        $kunjungan = Kunjungan::findOrFail($id);

        if ($kunjungan->image) {
            Storage::disk("public")->delete($kunjungan->image);
        }

        $kunjungan->delete();

        return response()->json([
            "success" => true,
            "message" => "Kunjungan berhasil dihapus",
        ]);
    }

    public function getKunjungan($id)
    {
        if (!$this->isAdmin()) {
            return response()->json(["success" => false, "message" => "Akses ditolak"], 403);
        }

        $kunjungan = Kunjungan::findOrFail($id);
        return response()->json($kunjungan);
    }

    public function magangAdmin()
    {
        if (!$this->isAdmin()) {
            return redirect()->route("login")->with("error", "Akses ditolak.");
        }
        $magangs = Magang::all();
        $whatsapp = Setting::get('whatsapp_admin', '6282240867746');
        return view("admin.magang", compact("magangs", "whatsapp"));
    }

    public function createMagang()
    {
        if (!$this->isAdmin()) {
            return redirect()->route("login")->with("error", "Akses ditolak.");
        }
        return view("admin.magang_form");
    }

    public function editMagangForm($id)
    {
        if (!$this->isAdmin()) {
            return redirect()->route("login")->with("error", "Akses ditolak.");
        }
        $magang = Magang::findOrFail($id);
        return view("admin.magang_form", compact("magang"));
    }

    public function addMagang(Request $request)
    {
        if (!$this->isAdmin()) {
            return redirect()->route("login")->with("error", "Akses ditolak.");
        }

        $validated = $request->validate([
            "name" => "required|string|unique:magangs,name",
            "price" => "required|numeric",
            "description" => "required|string",
            "image" => "nullable|image|mimes:jpeg,png,jpg,gif|max:10240",
            "is_wa_confirmation" => "required|boolean",
            "show_skill_description" => "required|boolean",
        ], [
            'name.unique' => 'Nama paket magang sudah ada, silakan gunakan nama lain.'
        ]);

        if ($request->hasFile("image")) {
            $uploaded = Cloudinary::upload($request->file("image")->getRealPath(), ['folder' => 'magangs']);
            $validated["image"] = $uploaded->getSecurePath();
        }

        Magang::create($validated);

        return redirect()->route('admin.magang-admin')->with('success', 'Magang berhasil ditambahkan');
    }

    public function editMagang(Request $request, $id)
    {
        if (!$this->isAdmin()) {
            return response()->json(["success" => false, "message" => "Akses ditolak"], 403);
        }

        $magang = Magang::findOrFail($id);

        $validated = $request->validate([
            "name" => "required|string|unique:magangs,name," . $id,
            "price" => "required|numeric",
            "description" => "required|string",
            "image" => "nullable|image|mimes:jpeg,png,jpg,gif|max:10240",
            "is_wa_confirmation" => "required|boolean",
            "show_skill_description" => "required|boolean",
        ], [
            'name.unique' => 'Nama paket magang sudah ada, silakan gunakan nama lain.'
        ]);

        if ($request->hasFile("image")) {
            $uploaded = Cloudinary::upload($request->file("image")->getRealPath(), ['folder' => 'magangs']);
            $validated["image"] = $uploaded->getSecurePath();
        }

        $magang->update($validated);
        return redirect()->route('admin.magang-admin')->with('success', 'Magang berhasil diperbarui');
    }

    public function deleteMagang($id)
    {
        if (!$this->isAdmin()) {
            return response()->json(["success" => false, "message" => "Akses ditolak"], 403);
        }

        $magang = Magang::findOrFail($id);
        if ($magang->image) {
            Storage::disk("public")->delete($magang->image);
        }
        $magang->delete();

        return response()->json(["success" => true, "message" => "Magang berhasil dihapus"]);
    }

    public function getMagang($id)
    {
        if (!$this->isAdmin()) {
            return response()->json(["success" => false, "message" => "Akses ditolak"], 403);
        }
        $magang = Magang::findOrFail($id);
        return response()->json($magang);
    }

    public function jadwalAdmin()
    {
        if (!$this->isAdmin()) {
            return redirect()->route("login")->with("error", "Akses ditolak.");
        }
        return view("admin.jadwal");
    }

    public function getJadwalEvents(Request $request)
    {
        if (!$this->isAdmin()) {
            return response()->json(["success" => false, "message" => "Akses ditolak"], 403);
        }

        $startLimit = Carbon::parse($request->input('start'))->startOfDay();
        $endLimit = Carbon::parse($request->input('end'))->endOfDay();

        $jadwals = Jadwal::where('kategori', 'libur')
            ->where('start_date', '<=', $endLimit)
            ->where('end_date', '>=', $startLimit)
            ->get();

        $kunjungans = DB::table('reservasi_kunjungan')
            ->join('users', 'reservasi_kunjungan.id_user', '=', 'users.id')
            ->join('kunjungans', 'reservasi_kunjungan.id_kunjungan', '=', 'kunjungans.id')
            ->select('reservasi_kunjungan.*', 'users.name as user_name', 'kunjungans.name as paket_name')
            ->where('tanggal_reservasi', '<=', $endLimit->format('Y-m-d'))
            ->where('tanggal_reservasi', '>=', $startLimit->format('Y-m-d'))
            ->whereNotIn('status_pembayaran', ['Dibatalkan Pengguna', 'Expired', 'Dibatalkan', 'Tidak Diterima'])
            ->get();

        $magangs = DB::table('pendaftaran_magang')
            ->join('users', 'pendaftaran_magang.id_user', '=', 'users.id')
            ->join('magangs', 'pendaftaran_magang.id_magang', '=', 'magangs.id')
            ->select('pendaftaran_magang.*', 'users.name as user_name', 'magangs.name as paket_name')
            ->whereNotIn('pendaftaran_magang.status_pembayaran', ['Dibatalkan Pengguna', 'Expired', 'Dibatalkan', 'Tidak Diterima'])
            ->get();

        $dailyData = [];

        // Helper to add data to daily map
        $addDaily = function ($date, $type, $item) use (&$dailyData) {
            if (!isset($dailyData[$date]))
                $dailyData[$date] = ['libur' => [], 'kunjungan' => [], 'PKL' => [], 'Umum' => []];
            $dailyData[$date][$type][] = $item;
        };

        // Process Libur
        foreach ($jadwals as $j) {
            $curr = Carbon::parse($j->start_date);
            $end = Carbon::parse($j->end_date ?? $j->start_date);
            while ($curr <= $end) {
                $d = $curr->format('Y-m-d');
                if ($curr >= $startLimit && $curr <= $endLimit) {
                    $addDaily($d, 'libur', ['id' => $j->id, 'title' => $j->title, 'desc' => $j->deskripsi]);
                }
                $curr->addDay();
            }
        }

        // Process Kunjungan
        foreach ($kunjungans as $k) {
            $addDaily($k->tanggal_reservasi, 'kunjungan', [
                'user' => $k->user_name,
                'paket' => $k->paket_name,
                'qty' => $k->jumlah_peserta
            ]);
        }

        // Process Magang
        foreach ($magangs as $m) {
            $isPKL = str_contains(strtolower($m->paket_name), 'pkl');
            $type = $isPKL ? 'PKL' : 'Umum';
            $curr = Carbon::parse($m->tanggal_magang);
            $end = (clone $curr)->addMonths($m->durasi_magang);

            while ($curr <= $end) {
                $d = $curr->format('Y-m-d');
                if ($curr >= $startLimit && $curr <= $endLimit) {
                    $addDaily($d, $type, [
                        'user' => $m->user_name,
                        'paket' => $m->paket_name,
                        'instansi' => $m->pekerjaan
                    ]);
                }
                $curr->addDay();
            }
        }

        $events = [];
        foreach ($dailyData as $date => $categories) {
            // Libur
            if (!empty($categories['libur'])) {
                $titles = collect($categories['libur'])->pluck('title')->unique()->implode(', ');
                $descs = collect($categories['libur'])->pluck('desc')->filter()->unique()->implode("\n");
                $originalId = $categories['libur'][0]['id'] ?? 0;

                $events[] = [
                    'id' => "libur-{$originalId}",
                    'title' => $titles,
                    'start' => $date,
                    'color' => '#fff1f1',
                    'className' => 'event-libur-bg',
                    'extendedProps' => ['kategori' => 'libur', 'deskripsi' => "HARI LIBUR: $titles\n$descs"]
                ];
            }

            // Kunjungan
            if (!empty($categories['kunjungan'])) {
                $details = "Daftar Kunjungan ($date):\n";
                foreach ($categories['kunjungan'] as $idx => $item) {
                    $details .= ($idx + 1) . ". {$item['user']} ({$item['paket']} - {$item['qty']} orang)\n";
                }
                $events[] = [
                    'id' => "kun-{$date}",
                    'title' => 'Kunjungan',
                    'start' => $date,
                    'color' => '#218838',
                    'className' => 'event-kunjungan',
                    'extendedProps' => ['kategori' => 'kunjungan', 'deskripsi' => $details]
                ];
            }

            // Magang PKL & Umum
            foreach (['PKL', 'Umum'] as $type) {
                if (!empty($categories[$type])) {
                    $details = "Daftar Magang $type ($date):\n";
                    foreach ($categories[$type] as $idx => $item) {
                        $details .= ($idx + 1) . ". {$item['user']} ({$item['paket']} - {$item['instansi']})\n";
                    }
                    $events[] = [
                        'id' => "mag-{$type}-{$date}",
                        'title' => "Magang $type",
                        'start' => $date,
                        'color' => $type == 'PKL' ? '#0056b3' : '#4db8ff',
                        'className' => $type == 'PKL' ? 'event-magang-pkl' : 'event-magang-umum',
                        'extendedProps' => ['kategori' => 'magang', 'tipe' => $type, 'deskripsi' => $details]
                    ];
                }
            }
        }

        return response()->json($events);
    }

    public function addJadwal(Request $request)
    {
        if (!$this->isAdmin()) {
            return response()->json(["success" => false, "message" => "Akses ditolak"], 403);
        }

        $validated = $request->validate([
            "title" => "required|string",
            "kategori" => "required|in:libur,kunjungan,magang",
            "deskripsi" => "nullable|string",
            "start_date" => "required|date",
            // We can pass end_date optionally
        ]);

        $validated['end_date'] = $request->end_date ? clone new \DateTime($request->end_date) : clone new \DateTime($request->start_date);

        $startDate = new \DateTime($request->start_date);
        $endDate = clone $validated['end_date'];

        if ($request->kategori == 'kunjungan') {
            $currentDate = clone $startDate;
            while ($currentDate <= $endDate) {
                if ($currentDate->format('N') == 7) {
                    return response()->json(["success" => false, "message" => "Kunjungan tidak bisa dijadwalkan pada hari libur (Minggu)"], 422);
                }
                $currentDate->modify('+1 day');
            }

            $overlap = Jadwal::where('kategori', 'libur')
                ->where('start_date', '<=', $endDate->format('Y-m-d 23:59:59'))
                ->where('end_date', '>=', $startDate->format('Y-m-d 00:00:00'));

            if ($overlap->exists()) {
                return response()->json(["success" => false, "message" => "Kunjungan tidak bisa dijadwalkan pada rentang hari libur yang sudah ditentukan"], 422);
            }
        }

        if ($request->kategori == 'libur') {
            // Cek apakah tanggal yang dipilih adalah tanggal lampau
            $today = Carbon::today();
            $start = Carbon::parse($request->start_date)->startOfDay();

            if ($start->lt($today)) {
                return response()->json(["success" => false, "message" => "Tidak dapat mengatur libur pada tanggal yang sudah berlalu"], 422);
            }

            // Cek tabel Jadwal untuk kategori kunjungan
            $overlapJadwal = Jadwal::where('kategori', 'kunjungan')
                ->where('start_date', '<=', $endDate->format('Y-m-d 23:59:59'))
                ->where('end_date', '>=', $startDate->format('Y-m-d 00:00:00'))
                ->get();

            // Cek tabel reservasi_kunjungan untuk reservasi aktif
            $overlappingVisits = DB::table('reservasi_kunjungan')
                ->join('users', 'reservasi_kunjungan.id_user', '=', 'users.id')
                ->join('kunjungans', 'reservasi_kunjungan.id_kunjungan', '=', 'kunjungans.id')
                ->select('reservasi_kunjungan.*', 'users.name as user_name', 'kunjungans.name as paket_name')
                ->where('tanggal_reservasi', '<=', $endDate->format('Y-m-d'))
                ->where('tanggal_reservasi', '>=', $startDate->format('Y-m-d'))
                ->whereNotIn('status_pembayaran', ['Dibatalkan Pengguna', 'Expired', 'Dibatalkan', 'Tidak Diterima'])
                ->get();

            if ($overlapJadwal->isNotEmpty() || $overlappingVisits->isNotEmpty()) {
                $visitDetails = "";

                foreach ($overlapJadwal as $j) {
                    $visitDetails .= "- Jadwal: {$j->title} (" . Carbon::parse($j->start_date)->format('d-m-Y') . ")\n";
                }

                foreach ($overlappingVisits as $visit) {
                    $visitDetails .= "- Reservasi: {$visit->user_name} ({$visit->paket_name}) pada " . Carbon::parse($visit->tanggal_reservasi)->format('d-m-Y') . "\n";
                }

                return response()->json([
                    "success" => false,
                    "message" => "Tidak dapat mengatur libur karena terdapat kunjungan pada tanggal tersebut:\n\n" . $visitDetails . "\nJika ingin meliburkan tanggal ini, silakan batalkan kunjungan tersebut dan beri tahu yang melakukan kunjungan."
                ], 422);
            }
        }

        if ($request->kategori == 'libur') {
            $currentDate = clone $startDate;
            while ($currentDate <= $endDate) {
                Jadwal::create([
                    'title' => $request->title,
                    'kategori' => 'libur',
                    'deskripsi' => $request->deskripsi,
                    'start_date' => $currentDate->format('Y-m-d 00:00:00'),
                    'end_date' => $currentDate->format('Y-m-d 23:59:59'),
                ]);
                $currentDate->modify('+1 day');
            }
            return response()->json(["success" => true, "message" => "Hari libur berhasil ditambahkan (Harian)"]);
        }

        Jadwal::create($validated);
        return response()->json(["success" => true, "message" => "Jadwal berhasil ditambahkan"]);
    }

    public function editJadwal(Request $request, $id)
    {
        if (!$this->isAdmin()) {
            return response()->json(["success" => false, "message" => "Akses ditolak"], 403);
        }
        $jadwal = Jadwal::findOrFail($id);

        $validated = $request->validate([
            "title" => "required|string",
            "kategori" => "required|in:libur,kunjungan,magang",
            "deskripsi" => "nullable|string",
            "start_date" => "required|date",
        ]);

        $validated['end_date'] = $request->end_date ? clone new \DateTime($request->end_date) : clone new \DateTime($request->start_date);

        $startDate = new \DateTime($request->start_date);
        $endDate = clone $validated['end_date'];

        if ($request->kategori == 'kunjungan') {
            $currentDate = clone $startDate;
            while ($currentDate <= $endDate) {
                if ($currentDate->format('N') == 7) {
                    return response()->json(["success" => false, "message" => "Kunjungan tidak bisa dijadwalkan pada hari libur (Minggu)"], 422);
                }
                $currentDate->modify('+1 day');
            }

            $overlap = Jadwal::where('kategori', 'libur')
                ->where('id', '!=', $id)
                ->where('start_date', '<=', $endDate->format('Y-m-d 23:59:59'))
                ->where('end_date', '>=', $startDate->format('Y-m-d 00:00:00'));

            if ($overlap->exists()) {
                return response()->json(["success" => false, "message" => "Kunjungan tidak bisa dijadwalkan pada rentang hari libur yang sudah ditentukan"], 422);
            }
        }

        if ($request->kategori == 'libur') {
            // Cek apakah tanggal yang dipilih adalah tanggal lampau
            $today = Carbon::today();
            $start = Carbon::parse($request->start_date)->startOfDay();

            if ($start->lt($today)) {
                return response()->json(["success" => false, "message" => "Tidak dapat mengatur libur pada tanggal yang sudah berlalu"], 422);
            }

            // Cek tabel Jadwal untuk kategori kunjungan
            $overlapJadwal = Jadwal::where('kategori', 'kunjungan')
                ->where('id', '!=', $id)
                ->where('start_date', '<=', $endDate->format('Y-m-d 23:59:59'))
                ->where('end_date', '>=', $startDate->format('Y-m-d 00:00:00'))
                ->get();

            // Cek tabel reservasi_kunjungan untuk reservasi aktif
            $overlappingVisits = DB::table('reservasi_kunjungan')
                ->join('users', 'reservasi_kunjungan.id_user', '=', 'users.id')
                ->join('kunjungans', 'reservasi_kunjungan.id_kunjungan', '=', 'kunjungans.id')
                ->select('reservasi_kunjungan.*', 'users.name as user_name', 'kunjungans.name as paket_name')
                ->where('tanggal_reservasi', '<=', $endDate->format('Y-m-d'))
                ->where('tanggal_reservasi', '>=', $startDate->format('Y-m-d'))
                ->whereNotIn('status_pembayaran', ['Dibatalkan Pengguna', 'Expired', 'Dibatalkan', 'Tidak Diterima'])
                ->get();

            if ($overlapJadwal->isNotEmpty() || $overlappingVisits->isNotEmpty()) {
                $visitDetails = "";

                foreach ($overlapJadwal as $j) {
                    $visitDetails .= "- Jadwal: {$j->title} (" . Carbon::parse($j->start_date)->format('d-m-Y') . ")\n";
                }

                foreach ($overlappingVisits as $visit) {
                    $visitDetails .= "- Reservasi: {$visit->user_name} ({$visit->paket_name}) pada " . Carbon::parse($visit->tanggal_reservasi)->format('d-m-Y') . "\n";
                }

                return response()->json([
                    "success" => false,
                    "message" => "Tidak dapat mengatur libur karena terdapat kunjungan pada tanggal tersebut:\n\n" . $visitDetails . "\nJika ingin meliburkan tanggal ini, silakan batalkan kunjungan tersebut dan beri tahu yang melakukan kunjungan."
                ], 422);
            }
        }

        $jadwal->update($validated);
        return response()->json(["success" => true, "message" => "Jadwal berhasil diperbarui"]);
    }

    public function deleteJadwal($id)
    {
        if (!$this->isAdmin()) {
            return response()->json(["success" => false, "message" => "Akses ditolak"], 403);
        }
        $jadwal = Jadwal::findOrFail($id);
        $jadwal->delete();
        return response()->json(["success" => true, "message" => "Jadwal berhasil dihapus"]);
    }

    public function getJadwal($id)
    {
        if (!$this->isAdmin()) {
            return response()->json(["success" => false, "message" => "Akses ditolak"], 403);
        }
        $jadwal = Jadwal::findOrFail($id);
        return response()->json($jadwal);
    }

    public function laporan(Request $request)
    {
        if (!$this->isAdmin()) {
            return redirect()->route("login")->with("error", "Akses ditolak.");
        }

        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-t'));
        $kategoriFilter = $request->input('kategori', 'Semua');
        $subMagangFilter = $request->input('sub_magang', 'Semua'); // PKL, Magang Umum
        $metodeBayarFilter = $request->input('metode_bayar', 'Semua'); // Tunai, QRIS
        $search = strtolower($request->input('search', ''));

        // 1. Transaksi Produk (Orders)
        $orderQuery = Order::with('items.product')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where(function($q) {
                // Jika Tunai: Hanya yang berstatus Selesai
                $q->where(function($sq) {
                    $sq->where('metode_pembayaran', 'Tunai')
                       ->where('status', 'Selesai');
                })
                // Jika QRIS: Masuk saat status Diproses, Sedang Dikemas, Dikirim, atau Selesai
                ->orWhere(function($sq) {
                    $sq->where('metode_pembayaran', '!=', 'Tunai') // Berarti QRIS
                       ->whereIn('status', ['Diproses', 'Sedang Dikemas', 'Dikirim', 'Pesanan Siap Diambil', 'Selesai']);
                });
            });

        if ($metodeBayarFilter != 'Semua') {
            $orderQuery->where('metode_pembayaran', $metodeBayarFilter);
        }
        $orders = $orderQuery->get();

        // 2. Kunjungan
        $kunjunganQuery = DB::table('reservasi_kunjungan')
            ->join('kunjungans', 'reservasi_kunjungan.id_kunjungan', '=', 'kunjungans.id')
            ->select('reservasi_kunjungan.*', 'kunjungans.name as paket_name')
            ->whereBetween('reservasi_kunjungan.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->whereIn('reservasi_kunjungan.status_pembayaran', ['Diterima', 'Selesai']);

        if ($metodeBayarFilter != 'Semua') {
            $kunjunganQuery->where('metode_pembayaran', $metodeBayarFilter);
        }
        $kunjungans = $kunjunganQuery->get();

        // 3. Pendaftaran (Magang)
        $magangQuery = DB::table('pendaftaran_magang')
            ->join('magangs', 'pendaftaran_magang.id_magang', '=', 'magangs.id')
            ->join('users', 'pendaftaran_magang.id_user', '=', 'users.id')
            ->select('pendaftaran_magang.*', 'magangs.name as paket_name', 'users.name as user_name')
            ->whereBetween('pendaftaran_magang.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->whereIn('pendaftaran_magang.status_pembayaran', ['Diterima', 'Selesai']);

        if ($metodeBayarFilter != 'Semua') {
            $magangQuery->where('metode_pembayaran', $metodeBayarFilter);
        }

        if ($subMagangFilter != 'Semua') {
            if ($subMagangFilter == 'PKL') {
                $magangQuery->where('magangs.name', 'LIKE', 'PKL%');
            } else {
                $magangQuery->where('magangs.name', 'LIKE', 'Magang Umum%');
            }
        }
        $magangs = $magangQuery->get();

        $laporans = collect([]);

        // Push Orders
        if ($kategoriFilter == 'Semua' || $kategoriFilter == 'Produk') {
            foreach ($orders as $o) {
                $detailItems = [];
                $namaProdukArr = [];
                $qtyArr = [];
                foreach ($o->items as $item) {
                    $prodName = $item->product ? $item->product->name : 'Produk Terhapus';
                    // Menampilkan harga per unit yang tersimpan di order_items (bukan harga produk sekarang)
                    $detailItems[] = $item->quantity . ' kg ' . $prodName . ' (+ ' . number_format($item->price, 0, ',', '.') . ')';
                    $namaProdukArr[] = $prodName;
                    $qtyArr[] = $item->quantity;
                }

                $laporans->push((object) [
                    'id_transaksi' => 'TRX-' . str_pad($o->id, 5, '0', STR_PAD_LEFT),
                    'tanggal' => Carbon::parse($o->created_at),
                    'kategori' => 'Produk',
                    'keterangan' => implode(', ', $detailItems),
                    'nama_produk' => implode('<br>', $namaProdukArr),
                    'qty' => implode('<br>', $qtyArr),
                    'metode_pembayaran' => $o->metode_pembayaran ?? 'QRIS',
                    'harga' => $o->grand_total,
                ]);
            }
        }

        // Push Kunjungan
        if ($kategoriFilter == 'Semua' || $kategoriFilter == 'Kunjungan') {
            foreach ($kunjungans as $k) {
                // Hitung harga satuan saat itu
                $unitPrice = ($k->jumlah_peserta > 0) ? ($k->total_harga / $k->jumlah_peserta) : 0;
                $laporans->push((object) [
                    'id_transaksi' => 'KUN-' . str_pad($k->id_reservasi, 5, '0', STR_PAD_LEFT),
                    'tanggal' => Carbon::parse($k->created_at),
                    'kategori' => 'Kunjungan',
                    'keterangan' => 'Kunjungan ' . $k->paket_name . ' (' . $k->jumlah_peserta . ' orang + ' . number_format($unitPrice, 0, ',', '.') . ')',
                    'jenis_kunjungan' => $k->paket_name,
                    'jumlah_peserta' => $k->jumlah_peserta . ' Orang',
                    'metode_pembayaran' => $k->metode_pembayaran ?? 'QRIS',
                    'harga' => $k->total_harga,
                ]);
            }
        }

        // Push Magang
        if ($kategoriFilter == 'Semua' || $kategoriFilter == 'Magang') {
            foreach ($magangs as $m) {
                $namaPeserta = $m->pekerjaan ? $m->user_name . ' (' . $m->pekerjaan . ')' : $m->user_name;
                // Hitung harga satuan (per bulan) saat itu
                $unitPrice = ($m->durasi_magang > 0) ? ($m->total_harga / $m->durasi_magang) : 0;
                $laporans->push((object) [
                    'id_transaksi' => 'MAG-' . str_pad($m->id_pendaftaran, 5, '0', STR_PAD_LEFT),
                    'tanggal' => Carbon::parse($m->created_at),
                    'kategori' => 'Magang',
                    'keterangan' => 'Pendaftaran ' . $m->paket_name . ' (' . $m->durasi_magang . ' bulan + ' . number_format($unitPrice, 0, ',', '.') . ') oleh ' . $namaPeserta,
                    'nama_individu_instansi' => $namaPeserta,
                    'jenis_magang' => $m->paket_name,
                    'metode_pembayaran' => $m->metode_pembayaran ?? 'QRIS',
                    'harga' => $m->total_harga,
                ]);
            }
        }

        $laporans = $laporans->sortBy('tanggal');

        // Filter Search
        if (!empty($search)) {
            $laporans = $laporans->filter(function ($item) use ($search) {
                $searchStr = $item->id_transaksi . ' ' . $item->kategori . ' ' . $item->metode_pembayaran . ' ' . $item->tanggal->format('d-m-Y') . ' ' . $item->tanggal->format('Y-m-d');

                if ($item->kategori == 'Produk') {
                    $searchStr .= ' ' . strip_tags($item->nama_produk) . ' ' . $item->keterangan;
                } elseif ($item->kategori == 'Kunjungan') {
                    $searchStr .= ' ' . $item->jenis_kunjungan . ' ' . $item->keterangan;
                } elseif ($item->kategori == 'Magang') {
                    $searchStr .= ' ' . $item->nama_individu_instansi . ' ' . $item->jenis_magang . ' ' . $item->keterangan;
                }

                return str_contains(strtolower($searchStr), $search);
            });
        }

        $total_pendapatan = $laporans->sum('harga');

        return view('admin.laporan_penjualan', [
            'laporans' => $laporans,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'total_pendapatan' => $total_pendapatan,
            'kategoriFilter' => $kategoriFilter,
            'subMagangFilter' => $subMagangFilter,
            'metodeBayarFilter' => $metodeBayarFilter,
            'search' => $search
        ]);
    }

    public function kunjunganManajemen(Request $request)
    {
        if (!$this->isAdmin()) {
            return redirect()->route("login")->with("error", "Akses ditolak.");
        }

        $statusFilter = $request->query('status');
        $search = strtolower($request->query('search', ''));
        $date = $request->query('date');

        // Base Query
        $kunjunganQuery = DB::table('reservasi_kunjungan')
            ->leftJoin('users', 'reservasi_kunjungan.id_user', '=', 'users.id')
            ->leftJoin('kunjungans', 'reservasi_kunjungan.id_kunjungan', '=', 'kunjungans.id')
            ->select('reservasi_kunjungan.*', 'users.name as user_name', 'users.username as user_username', 'kunjungans.name as paket_name')
            ->whereNotIn('reservasi_kunjungan.status_pembayaran', ['Expired', 'Dibatalkan Pengguna'])
            ->orderBy('reservasi_kunjungan.created_at', 'desc');

        $allKunjungans = $kunjunganQuery->get();
        $filteredKunjungans = collect([]);

        foreach ($allKunjungans as $k) {
            $isPast = Carbon::parse($k->tanggal_reservasi)->startOfDay() < Carbon::today();

            // Jika kunjungan sudah lewat dan statusnya 'Diterima', ubah otomatis ke 'Selesai'
            if ($isPast && $k->status_pembayaran == 'Diterima') {
                $k->status_pembayaran = 'Selesai';
            }

            // Tentukan Filter Dasar (Aktif, Selesai, Dibatalkan)
            $isBatal = in_array($k->status_pembayaran, ['Dibatalkan', 'Tidak Diterima']);

            if ($isBatal) {
                $k->status_kunjungan_filter = 'Dibatalkan';
            } elseif ($isPast) {
                $k->status_kunjungan_filter = 'Selesai';
            } else {
                $k->status_kunjungan_filter = 'Aktif';
            }

            // Logika Pencocokan Filter Tab
            $statusMatch = false;
            if ($statusFilter == 'offline') {
                $statusMatch = ($k->is_offline == 1);
            } else {
                if (!$statusFilter || $statusFilter == '' || $statusFilter == 'Semua') {
                    $statusMatch = ($k->is_offline == 0);
                } elseif ($statusFilter == 'Diterima') {
                    $statusMatch = ($k->status_pembayaran == 'Diterima');
                } else {
                    $statusMatch = ($k->status_kunjungan_filter == $statusFilter);
                }
            }

            // Filter Pencarian
            $searchMatch = true;
            if (!empty($search)) {
                $kunId = 'kun-' . str_pad($k->id_reservasi, 4, '0', STR_PAD_LEFT);
                $searchStr = strtolower($kunId . ' ' . $k->user_name . ' ' . $k->user_username . ' ' . $k->paket_name . ' ' . $k->instansi . ' ' . $k->status_pembayaran);
                $searchMatch = str_contains($searchStr, $search);
            }

            // Filter Tanggal
            $dateMatch = (!$date || $k->tanggal_reservasi == $date);

            if ($statusMatch && $searchMatch && $dateMatch) {
                $filteredKunjungans->push($k);
            }
        }

        // Sorting Logika
        if ($statusFilter == 'Aktif') {
            $kunjungans = $filteredKunjungans->sortBy('tanggal_reservasi')->values();
        } elseif ($statusFilter == 'Diterima') {
            // Diterima: Urutkan berdasarkan tanggal kunjungan terdekat (Terkecil di atas)
            $kunjungans = $filteredKunjungans->sortBy('tanggal_reservasi')->values();
        } elseif (!$statusFilter || $statusFilter == 'Semua') {
            $kunjungans = $filteredKunjungans->sortByDesc('created_at')->values();
        } else {
            $kunjungans = $filteredKunjungans->sortByDesc('created_at')->values();
        }

        return view("admin.kunjungan_manajemen", compact("kunjungans", "statusFilter", "search", "date"));
    }

    // public function magangManajemen()
    // {
    //     if (!$this->isAdmin()) {
    //         return redirect()->route("login")->with("error", "Akses ditolak.");
    //     }

    //     $magangs = DB::table('pendaftaran_magang')
    //         ->join('users', 'pendaftaran_magang.id_user', '=', 'users.id')
    //         ->join('magangs', 'pendaftaran_magang.id_magang', '=', 'magangs.id')
    //         ->select('pendaftaran_magang.*', 'users.name as user_name', 'users.username as user_username', 'magangs.name as paket_name')
    //         ->orderBy('pendaftaran_magang.created_at', 'desc')
    //         ->get();

    //     foreach ($magangs as $m) {
    //         $endDate = Carbon::parse($m->tanggal_magang)->addMonths($m->durasi_magang)->startOfDay();
    //         $m->status_magang = (Carbon::today() > $endDate) ? 'Selesai' : 'Aktif';
    //     }

    //     return view("admin.magang_manajemen", compact("magangs"));
    // }

    public function magangManajemen(Request $request)
    {
        if (!$this->isAdmin()) {
            return redirect()->route("login")->with("error", "Akses ditolak.");
        }

        $statusFilter = $request->query('status');
        $subMagangFilter = $request->query('sub_magang', 'Semua');
        $search = strtolower($request->query('search', ''));
        $date = $request->query('date');

        $magangQuery = DB::table('pendaftaran_magang')
            ->leftJoin('users', 'pendaftaran_magang.id_user', '=', 'users.id')
            ->leftJoin('magangs', 'pendaftaran_magang.id_magang', '=', 'magangs.id')
            ->select(
                'pendaftaran_magang.*',
                'pendaftaran_magang.id_pendaftaran as id',
                'users.name as user_name',
                'users.username as user_username',
                'magangs.name as paket_name'
            )
            ->where(function ($q) {
                $q->whereNotIn('pendaftaran_magang.status_pembayaran', ['Expired', 'Dibatalkan Pengguna', 'Dibatalkan'])
                    ->orWhereNull('pendaftaran_magang.status_pembayaran');
            });

        // Filter Sub Magang (PKL / Umum)
        if ($subMagangFilter != 'Semua') {
            if ($subMagangFilter == 'PKL') {
                $magangQuery->where('magangs.name', 'LIKE', 'PKL%');
            } else {
                $magangQuery->where('magangs.name', 'LIKE', 'Magang Umum%');
            }
        }

        $magangQuery->orderBy('pendaftaran_magang.created_at', 'desc');

        $magangs = $magangQuery->get();

        $filteredMagangs = collect([]);
        foreach ($magangs as $m) {
            // 0. Cek Expired QRIS (Jika Menunggu Pembayaran dan expires_at sudah lewat)
            if ($m->status_pembayaran == 'Menunggu Pembayaran' && $m->expires_at && \Carbon\Carbon::parse($m->expires_at)->isPast()) {
                DB::table('pendaftaran_magang')
                    ->where('id_pendaftaran', $m->id_pendaftaran)
                    ->update(['status_pembayaran' => 'Expired', 'expires_at' => null, 'updated_at' => now()]);
                $m->status_pembayaran = 'Expired';
            }

            // Pesanan Expired/Dibatalkan tidak boleh terlihat oleh admin
            if (in_array($m->status_pembayaran, ['Expired', 'Dibatalkan', 'Dibatalkan Pengguna']))
                continue;

            $startDate = Carbon::parse($m->tanggal_magang)->startOfDay();
            $endDate = Carbon::parse($m->tanggal_magang)->addMonths($m->durasi_magang)->endOfDay();

            // Logika Otomatis: Jika sudah lewat tanggal selesai dan statusnya 'Diterima', ubah ke 'Selesai'
            if (Carbon::today() > $endDate && $m->status_pembayaran == 'Diterima') {
                DB::table('pendaftaran_magang')
                    ->where('id_pendaftaran', $m->id_pendaftaran)
                    ->update(['status_pembayaran' => 'Selesai', 'updated_at' => now()]);
                $m->status_pembayaran = 'Selesai'; // Update object untuk tampilan saat ini
            }

            // Logika Otomatis: Jika PKL masih "Menunggu Konfirmasi" sampai hari H mulai, maka otomatis "Dibatalkan"
            if (strtoupper($m->paket_name) == 'PKL' && $m->status_pembayaran == 'Menunggu Konfirmasi' && Carbon::today() >= $startDate) {
                DB::table('pendaftaran_magang')
                    ->where('id_pendaftaran', $m->id_pendaftaran)
                    ->update(['status_pembayaran' => 'Dibatalkan', 'updated_at' => now()]);
                $m->status_pembayaran = 'Dibatalkan';
            }

            // Logika Status Magang Berdasarkan Tanggal & Status Pembayaran untuk Filter Tab
            if ($m->status_pembayaran == 'Dibatalkan' || $m->status_pembayaran == 'Tidak Diterima') {
                $m->status_magang_filter = 'Dibatalkan';
            } else {
                if (Carbon::today() < $startDate) {
                    $m->status_magang_filter = 'Belum Mulai';
                } elseif (Carbon::today() <= $endDate) {
                    // Hanya dianggap "Aktif" jika sudah Diterima (sesuai permintaan user, Lunas tidak otomatis Aktif)
                    if ($m->status_pembayaran == 'Diterima') {
                        $m->status_magang_filter = 'Aktif';
                        $m->status_pembayaran = 'Aktif';
                    } else {
                        $m->status_magang_filter = 'Menunggu';
                    }
                } else {
                    $m->status_magang_filter = 'Selesai';
                }
            }

            // Penyesuaian Filter Status Match
            if ($statusFilter == 'offline') {
                $statusMatch = ($m->is_offline == 1);
            } else {
                if ($statusFilter == 'Diterima') {
                    $statusMatch = ($m->status_pembayaran == 'Diterima');
                } else {
                    $statusMatch = (!$statusFilter || $statusFilter == 'Semua' || $m->status_magang_filter == $statusFilter);
                }

                if (!$statusFilter || $statusFilter == 'Semua') {
                    $statusMatch = $statusMatch && ($m->is_offline == 0);
                }
            }

            $dateMatch = true;
            if ($date) {
                $checkDate = Carbon::parse($date);
                $dateMatch = $checkDate->between($startDate, $endDate);
            }

            $searchMatch = true;
            if (!empty($search)) {
                // Menambahkan format MAG-ID, id_user, tanggal_magang, dan tanggal hari (1-31) ke jangkauan pencarian
                $magangId = 'mag-' . str_pad($m->id, 4, '0', STR_PAD_LEFT);
                $dayStr = \Carbon\Carbon::parse($m->tanggal_magang)->format('d');
                $searchStr = strtolower($magangId . ' ' . $m->id . ' ' . $m->id_user . ' ' . $m->user_name . ' ' . $m->user_username . ' ' . $m->paket_name . ' ' . $m->tanggal_magang . ' ' . $dayStr . ' ' . $m->status_pembayaran);
                $searchMatch = str_contains($searchStr, $search);
            }

            // Hitung Sisa Hari untuk Sorting
            if (isset($m->status_magang_filter) && $m->status_magang_filter == 'Belum Mulai') {
                $m->sisa_hari = (int) Carbon::today()->diffInDays($startDate);
            } elseif (isset($m->status_magang_filter) && $m->status_magang_filter == 'Aktif') {
                $m->sisa_hari = (int) Carbon::today()->diffInDays($endDate);
            } else {
                $m->sisa_hari = 999999; // Untuk yang sudah selesai atau batal, taruh di bawah
            }

            if ($statusMatch && $searchMatch && $dateMatch) {
                $filteredMagangs->push($m);
            }
        }

        // Urutkan:
        // Jika filter "Semua" (statusFilter kosong), urutkan berdasarkan pendaftaran terbaru (created_at DESC)
        // Jika filter tertentu, urutkan berdasarkan sisa hari terkecil
        if (!$statusFilter || $statusFilter == 'Semua') {
            $magangs = $filteredMagangs->sortByDesc('created_at');
        } elseif ($statusFilter == 'Diterima') {
            // Diterima: Urutkan berdasarkan tanggal magang terdekat (Terkecil di atas)
            $magangs = $filteredMagangs->sortBy('tanggal_magang');
        } else {
            $magangs = $filteredMagangs->sortBy('sisa_hari');
        }

        return view("admin.magang_manajemen", compact("magangs", "statusFilter", "search", "date", "subMagangFilter"));
    }

    public function updateStatusKunjungan(Request $request, $id)
    {
        if (!$this->isAdmin()) {
            return response()->json(["success" => false, "message" => "Akses ditolak"], 403);
        }
        $request->validate([
            'status' => 'required|string'
        ]);

        DB::table('reservasi_kunjungan')
            ->where('id_reservasi', $id)
            ->update(['status_pembayaran' => $request->status, 'updated_at' => now()]);

        return response()->json(["success" => true, "message" => "Status pembayaran kunjungan berhasil diperbarui"]);
    }

    public function updateStatusMagang(Request $request, $id)
    {
        if (!$this->isAdmin()) {
            return response()->json(["success" => false, "message" => "Akses ditolak"], 403);
        }
        $request->validate([
            'status' => 'required|string'
        ]);

        $update = [
            'status_pembayaran' => $request->status,
            'updated_at' => now(),
        ];

        if ($request->status === 'Terkonfirmasi') {
            $update['expires_at'] = null;
            $update['midtrans_order_id'] = null;
        }

        DB::table('pendaftaran_magang')
            ->where('id_pendaftaran', $id)
            ->update($update);

        return response()->json(["success" => true, "message" => "Status pendaftaran berhasil diperbarui"]);
    }
    public function settings()
    {
        if (!$this->isAdmin()) {
            return redirect()->route("login")->with("error", "Akses ditolak.");
        }
        $whatsapp = Setting::get('whatsapp_admin', '6282240867746');
        $qris_image = Setting::get('qris_image', '');
        return view("admin.settings", compact("whatsapp", "qris_image"));
    }

    public function updateSettings(Request $request)
    {
        if (!$this->isAdmin()) {
            return response()->json(["success" => false, "message" => "Akses ditolak"], 403);
        }

        if ($request->has('whatsapp_admin')) {
            $request->validate(['whatsapp_admin' => 'required|string']);
            $number = preg_replace('/[^0-9]/', '', $request->whatsapp_admin);
            if (str_starts_with($number, '0')) {
                $number = '62' . substr($number, 1);
            }
            Setting::set('whatsapp_admin', $number);
        }

        if ($request->hasFile('qris_image')) {
            $request->validate(['qris_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240']);
            $uploaded = Cloudinary::upload($request->file('qris_image')->getRealPath(), ['folder' => 'settings']);
            Setting::set('qris_image', $uploaded->getSecurePath());
        }

        return response()->json(["success" => true, "message" => "Pengaturan berhasil diperbarui"]);
    }

    public function searchUsersOfflineTransaksi(Request $request)
    {
        if (!$this->isAdmin()) {
            return response()->json(["success" => false, "message" => "Akses ditolak"], 403);
        }

        $search = $request->input('search', '');
        $type = $request->input('type', 'online'); // 'online' atau 'offline'

        $query = User::where('role', 'user');

        if ($type === 'offline') {
            $query->where('username', 'like', 'offline_%');
        } else {
            $query->where(function($q) {
                $q->where('username', 'not like', 'offline_%')
                  ->orWhereNull('username');
            });
        }

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('username', 'like', '%' . $search . '%');
            });
        }

        $users = $query->limit(20)->get(['id', 'name', 'username', 'nohp', 'email']);

        return response()->json($users);
    }
    public function transaksiOffline(Request $request)
    {
        if (!$this->isAdmin()) {
            return redirect()->route("login")->with("error", "Akses ditolak.");
        }

        $products = Product::orderBy('name', 'asc')->get();
        $kunjungans = DB::table('kunjungans')->orderBy('name', 'asc')->get();
        $magangs = DB::table('magangs')->orderBy('name', 'asc')->get();

        return view("admin.transaksi_offline", compact("products", "kunjungans", "magangs"));
    }

    public function storeTransaksiOfflineProduk(Request $request)
    {
        if (!$this->isAdmin()) {
            return response()->json(["success" => false, "message" => "Akses ditolak"], 403);
        }

        $request->validate([
            'tipe_pembeli_prod' => 'required|in:online,offline',
            'metode_pembayaran' => 'required|in:tunai,qris',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        if ($request->tipe_pembeli_prod === 'online') {
            $request->validate(['user_id_online_prod' => 'required|exists:users,id']);
        } else {
            $request->validate(['nama_pembeli_offline_prod' => 'required|string|max:255']);
        }

        DB::beginTransaction();
        try {
            $totalQty = 0;
            $grandTotal = 0;
            $itemsData = [];

            // 1. Validasi stok untuk semua produk terlebih dahulu
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                if ($product->stock < $item['quantity']) {
                    return response()->json([
                        "success" => false,
                        "message" => "Stok produk '" . $product->name . "' tidak mencukupi. Stok saat ini: " . $product->stock . " kg"
                    ], 400);
                }

                $subtotal = $product->price * $item['quantity'];
                $totalQty += $item['quantity'];
                $grandTotal += $subtotal;

                $itemsData[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                ];
            }

            // 2. Gunakan User Existing atau Buat User Offline
            if ($request->tipe_pembeli_prod === 'online') {
                $user = User::findOrFail($request->user_id_online_prod);
            } else {
                $nama = $request->nama_pembeli_offline_prod;
                $user = User::where('name', $nama)->where('role', 'user')->where('username', 'like', 'offline_%')->first();
                if (!$user) {
                    $username = 'offline_prod_' . time() . '_' . rand(100, 999);
                    $email = $username . '@nazfram.com';
                    $user = User::create([
                        'name' => $nama,
                        'username' => $username,
                        'email' => $email,
                        'role' => 'user',
                        'status' => 'active',
                        'password' => bcrypt(\Illuminate\Support\Str::random(10)),
                        'nohp' => $request->input('no_hp_prod', '-'),
                        'alamat' => 'Ambil Di Tempat (Offline)',
                    ]);
                } elseif ($request->filled('no_hp_prod')) {
                    $user->update(['nohp' => $request->no_hp_prod]);
                }
            }

            // 3. Buat Order
            $order = Order::create([
                'user_id' => $user->id,
                'total_produk' => $totalQty,
                'ongkir' => 0,
                'grand_total' => $grandTotal,
                'metode_pembayaran' => $request->metode_pembayaran,
                'metode_pengiriman' => 'Ambil Di Tempat',
                'alamat' => 'Ambil Di Tempat (Offline)',
                'jarak' => 0,
                'status' => 'Selesai',
                'is_offline' => 1,
            ]);

            // 4. Buat OrderItems & Decrement Stock
            foreach ($itemsData as $itemData) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $itemData['product']->id,
                    'quantity' => $itemData['quantity'],
                    'price' => $itemData['price'],
                ]);

                $itemData['product']->decrement('stock', $itemData['quantity']);
            }

            DB::commit();
            return response()->json(["success" => true, "message" => "Transaksi produk offline berhasil dicatat!"]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(["success" => false, "message" => "Terjadi kesalahan: " . $e->getMessage()], 500);
        }
    }

    public function storeTransaksiOfflineKunjungan(Request $request)
    {
        if (!$this->isAdmin()) {
            return response()->json(["success" => false, "message" => "Akses ditolak"], 403);
        }

        $request->validate([
            'tipe_pembeli_kun'  => 'required|in:online,offline',
            'instansi'        => 'required|string|max:255',
            'tanggal_reservasi' => 'required|date',
            'id_kunjungan'    => 'required|exists:kunjungans,id',
            'jumlah_peserta'  => 'required|integer|min:1',
            'metode_pembayaran' => 'required|in:tunai,qris',
        ]);

        if ($request->tipe_pembeli_kun === 'online') {
            $request->validate(['user_id_online_kun' => 'required|exists:users,id']);
        } else {
            $request->validate([
                'nama_pembeli_offline_kun' => 'required|string|max:255',
                'no_wa'           => 'required|string|max:20'
            ]);
        }

        $paket = DB::table('kunjungans')->where('id', $request->id_kunjungan)->first();

        if ($request->jumlah_peserta < $paket->min_people) {
            return response()->json(["success" => false, "message" => "Jumlah peserta minimal untuk paket ini adalah " . $paket->min_people . " orang."], 400);
        }
        if ($paket->max_people && $request->jumlah_peserta > $paket->max_people) {
            return response()->json(["success" => false, "message" => "Jumlah peserta maksimal untuk paket ini adalah " . $paket->max_people . " orang."], 400);
        }

        DB::beginTransaction();
        try {
            if ($request->tipe_pembeli_kun === 'online') {
                $user = User::findOrFail($request->user_id_online_kun);
            } else {
                $nama = $request->nama_pembeli_offline_kun;
                $user = User::where('name', $nama)->where('role', 'user')->where('username', 'like', 'offline_%')->first();
                if (!$user) {
                    $username = 'offline_kun_' . time() . '_' . rand(100, 999);
                    $email = $username . '@nazfram.com';
                    $user = User::create([
                        'name'     => $nama,
                        'username' => $username,
                        'email'    => $email,
                        'role'     => 'user',
                        'status'   => 'active',
                        'password' => bcrypt(\Illuminate\Support\Str::random(10)),
                        'nohp'     => $request->no_wa,
                        'alamat'   => $request->instansi,
                    ]);
                } else {
                    if ($request->filled('no_wa')) $user->update(['nohp' => $request->no_wa]);
                    $user->update(['alamat' => $request->instansi]);
                }
            }

            $total_harga = $paket->price * $request->jumlah_peserta;

            DB::table('reservasi_kunjungan')->insert([
                'id_user' => $user->id,
                'id_kunjungan' => $paket->id,
                'tanggal_reservasi' => $request->tanggal_reservasi,
                'jumlah_peserta' => $request->jumlah_peserta,
                'instansi' => $request->instansi,
                'total_harga' => $total_harga,
                'metode_pembayaran' => $request->metode_pembayaran,
                'status_pembayaran' => 'Diterima',
                'is_offline' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();
            return response()->json(["success" => true, "message" => "Transaksi kunjungan offline berhasil dicatat!"]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(["success" => false, "message" => "Terjadi kesalahan: " . $e->getMessage()], 500);
        }
    }

    public function storeTransaksiOfflineMagang(Request $request)
    {
        if (!$this->isAdmin()) {
            return response()->json(["success" => false, "message" => "Akses ditolak"], 403);
        }

        $request->validate([
            'tipe_pembeli_mag'  => 'required|in:online,offline',
            'instansi'        => 'required|string|max:255',
            'tanggal_magang'  => 'required|date',
            'id_magang'       => 'required|exists:magangs,id',
            'durasi_magang'   => 'required|integer|min:1',
            'metode_pembayaran' => 'required|in:tunai,qris',
        ]);

        if ($request->tipe_pembeli_mag === 'online') {
            $request->validate(['user_id_online_mag' => 'required|exists:users,id']);
        } else {
            $request->validate([
                'nama_pembeli_offline_mag' => 'required|string|max:255',
                'no_wa'           => 'required|string|max:20'
            ]);
        }

        $paket = DB::table('magangs')->where('id', $request->id_magang)->first();

        DB::beginTransaction();
        try {
            if ($request->tipe_pembeli_mag === 'online') {
                $user = User::findOrFail($request->user_id_online_mag);
            } else {
                $nama = $request->nama_pembeli_offline_mag;
                $user = User::where('name', $nama)->where('role', 'user')->where('username', 'like', 'offline_%')->first();
                if (!$user) {
                    $username = 'offline_mag_' . time() . '_' . rand(100, 999);
                    $email = $username . '@nazfram.com';
                    $user = User::create([
                        'name'     => $nama,
                        'username' => $username,
                        'email'    => $email,
                        'role'     => 'user',
                        'status'   => 'active',
                        'password' => bcrypt(\Illuminate\Support\Str::random(10)),
                        'nohp'     => $request->no_wa,
                        'alamat'   => $request->instansi,
                    ]);
                } else {
                    if ($request->filled('no_wa')) $user->update(['nohp' => $request->no_wa]);
                    $user->update(['alamat' => $request->instansi]);
                }
            }

            $total_harga = $paket->price * $request->durasi_magang;

            DB::table('pendaftaran_magang')->insert([
                'id_user' => $user->id,
                'id_magang' => $paket->id,
                'pekerjaan' => $request->instansi,
                'tanggal_magang' => $request->tanggal_magang,
                'durasi_magang' => $request->durasi_magang,
                'deskripsi_kemampuan' => 'Pendaftaran Offline',
                'metode_pembayaran' => $request->metode_pembayaran,
                'status_pembayaran' => 'Diterima',
                'total_harga' => $total_harga,
                'is_offline' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();
            return response()->json(["success" => true, "message" => "Transaksi magang offline berhasil dicatat!"]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(["success" => false, "message" => "Terjadi kesalahan: " . $e->getMessage()], 500);
        }
    }
}
