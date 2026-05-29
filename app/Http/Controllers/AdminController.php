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
        $kunjunganAkanDatangCount = DB::table('pendaftaran_kunjungan')
            ->whereBetween('tanggal_kunjungan', [$today->toDateString(), $threeDaysLater->toDateString()])
            ->where('status_pembayaran', 'Diterima')
            ->count();

        // 3. Magang Aktif (Tanggal mulai sudah lewat/hari ini, dan belum berakhir)
        $magangAktifCount = DB::table('pendaftaran_magang')
            ->where('status_pembayaran', 'Diterima')
            ->where(function($query) use ($today) {
                $query->whereRaw("DATE_ADD(tanggal_magang, INTERVAL durasi_magang MONTH) >= ?", [$today->toDateString()]);
            })
            ->count();

        // 4. Pengguna Terdaftar (Role user)
        $penggunaTerdaftarCount = User::where('role', 'user')->count();

        // Ambil data untuk grafik pendapatan (6 bulan terakhir)
        $pendapatanBulanan = [];
        for ($i = 5; $i >= 0; $i--) {
            $bulan = Carbon::now()->subMonths($i);
            $namaBulan = $bulan->translatedFormat('F Y');

            // Total dari Order (Toko)
            $totalOrder = Order::whereMonth('created_at', $bulan->month)
                ->whereYear('created_at', $bulan->year)
                ->where('status', 'Selesai')
                ->sum('total_price');

            // Total dari Kunjungan
            $totalKunjungan = DB::table('pendaftaran_kunjungan')
                ->whereMonth('created_at', $bulan->month)
                ->whereYear('created_at', $bulan->year)
                ->where('status_pembayaran', 'Diterima')
                ->sum('total_harga');

            // Total dari Magang
            $totalMagang = DB::table('pendaftaran_magang')
                ->whereMonth('created_at', $bulan->month)
                ->whereYear('created_at', $bulan->year)
                ->where('status_pembayaran', 'Diterima')
                ->sum('total_harga');

            $pendapatanBulanan[] = [
                'bulan' => $namaBulan,
                'total' => $totalOrder + $totalKunjungan + $totalMagang
            ];
        }

        return view(
            "admin.index",
            compact(
                "pesananHariIniCount",
                "kunjunganAkanDatangCount",
                "magangAktifCount",
                "penggunaTerdaftarCount",
                "pendapatanBulanan"
            )
        );
    }

    private function isAdmin()
    {
        return Auth::check() && Auth::user()->role === "admin";
    }

    // MANAJEMEN USER
    public function users()
    {
        if (!$this->isAdmin()) {
            return redirect()
                ->route("login")
                ->with("error", "Silakan login sebagai admin.");
        }
        $users = User::all();
        return view("admin.users", compact("users"));
    }

    public function toggleBlockUser($id)
    {
        if (!$this->isAdmin()) {
            return redirect()
                ->route("login")
                ->with("error", "Silakan login sebagai admin.");
        }
        $user = User::findOrFail($id);
        $user->is_blocked = !$user->is_blocked;
        $user->save();

        $status = $user->is_blocked ? "diblokir" : "diaktifkan kembali";
        return redirect()
            ->back()
            ->with("success", "Pengguna berhasil {$status}.");
    }

    // MANAJEMEN PRODUK
    public function products()
    {
        if (!$this->isAdmin()) {
            return redirect()
                ->route("login")
                ->with("error", "Silakan login sebagai admin.");
        }
        $products = Product::all();
        return view("admin.products", compact("products"));
    }

    public function storeProduct(Request $request)
    {
        if (!$this->isAdmin()) {
            return redirect()
                ->route("login")
                ->with("error", "Silakan login sebagai admin.");
        }
        $validated = $request->validate([
            "name" => "required|string|max:255",
            "description" => "required|string",
            "price" => "required|numeric|min:0",
            "stock" => "required|integer|min:0",
            "weight" => "required|numeric|min:0",
            "image" => "required|image|mimes:jpeg,png,jpg,gif|max:2048",
        ]);

        if ($request->hasFile("image")) {
            $uploadedUrl = $this->uploadToCloudinary(
                $request->file("image"),
                "products"
            );
            if (!$uploadedUrl) {
                return redirect()->back()->withErrors(['image' => 'Gagal mengunggah gambar ke Cloudinary. Silakan periksa kredensial atau koneksi internet Anda.'])->withInput();
            }
            $validated["image"] = $uploadedUrl;
        }

        Product::create($validated);

        return redirect()
            ->route("admin.products")
            ->with("success", "Produk berhasil ditambahkan.");
    }

    public function updateProduct(Request $request, $id)
    {
        if (!$this->isAdmin()) {
            return redirect()
                ->route("login")
                ->with("error", "Silakan login sebagai admin.");
        }
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            "name" => "required|string|max:255",
            "description" => "required|string",
            "price" => "required|numeric|min:0",
            "stock" => "required|integer|min:0",
            "weight" => "required|numeric|min:0",
            "image" => "nullable|image|mimes:jpeg,png,jpg,gif|max:2048",
        ]);

        if ($request->hasFile("image")) {
            $uploadedUrl = $this->uploadToCloudinary(
                $request->file("image"),
                "products"
            );
            if (!$uploadedUrl) {
                return redirect()->back()->withErrors(['image' => 'Gagal mengunggah gambar baru ke Cloudinary.'])->withInput();
            }
            $this->deleteFromCloudinary($product->image);
            $validated["image"] = $uploadedUrl;
        }

        $product->update($validated);

        return redirect()
            ->route("admin.products")
            ->with("success", "Produk berhasil diperbarui.");
    }

    public function destroyProduct($id)
    {
        if (!$this->isAdmin()) {
            return redirect()
                ->route("login")
                ->with("error", "Silakan login sebagai admin.");
        }
        $product = Product::findOrFail($id);
        $this->deleteFromCloudinary($product->image);
        $product->delete();

        return redirect()
            ->route("admin.products")
            ->with("success", "Produk berhasil dihapus.");
    }

    // MANAJEMEN KUNJUNGAN Edukasi
    public function kunjungan()
    {
        if (!$this->isAdmin()) {
            return redirect()
                ->route("login")
                ->with("error", "Silakan login sebagai admin.");
        }
        $kunjunganPakets = Kunjungan::all();
        return view("admin.kunjungan", compact("kunjunganPakets"));
    }

    public function storeKunjungan(Request $request)
    {
        if (!$this->isAdmin()) {
            return redirect()
                ->route("login")
                ->with("error", "Silakan login sebagai admin.");
        }
        $validated = $request->validate([
            "title" => "required|string|max:255",
            "description" => "required|string",
            "price" => "required|numeric|min:0",
            "image" => "required|image|mimes:jpeg,png,jpg,gif|max:2048",
        ]);

        if ($request->hasFile("image")) {
            $uploadedUrl = $this->uploadToCloudinary(
                $request->file("image"),
                "kunjungan"
            );
            if (!$uploadedUrl) {
                return redirect()->back()->withErrors(['image' => 'Gagal mengunggah gambar ke Cloudinary.'])->withInput();
            }
            $validated["image"] = $uploadedUrl;
        }

        Kunjungan::create($validated);

        return redirect()
            ->route("admin.kunjungan")
            ->with("success", "Paket Kunjungan berhasil ditambahkan.");
    }

    public function updateKunjungan(Request $request, $id)
    {
        if (!$this->isAdmin()) {
            return redirect()
                ->route("login")
                ->with("error", "Silakan login sebagai admin.");
        }
        $kunjungan = Kunjungan::findOrFail($id);

        $validated = $request->validate([
            "title" => "required|string|max:255",
            "description" => "required|string",
            "price" => "required|numeric|min:0",
            "image" => "nullable|image|mimes:jpeg,png,jpg,gif|max:2048",
        ]);

        if ($request->hasFile("image")) {
            $uploadedUrl = $this->uploadToCloudinary(
                $request->file("image"),
                "kunjungan"
            );
            if (!$uploadedUrl) {
                return redirect()->back()->withErrors(['image' => 'Gagal mengunggah gambar baru ke Cloudinary.'])->withInput();
            }
            $this->deleteFromCloudinary($kunjungan->image);
            $validated["image"] = $uploadedUrl;
        }

        $kunjungan->update($validated);

        return redirect()
            ->route("admin.kunjungan")
            ->with("success", "Paket Kunjungan berhasil diperbarui.");
    }

    public function destroyKunjungan($id)
    {
        if (!$this->isAdmin()) {
            return redirect()
                ->route("login")
                ->with("error", "Silakan login sebagai admin.");
        }
        $kunjungan = Kunjungan::findOrFail($id);
        $this->deleteFromCloudinary($kunjungan->image);
        $kunjungan->delete();

        return redirect()
            ->route("admin.kunjungan")
            ->with("success", "Paket Kunjungan berhasil dihapus.");
    }

    // MANAJEMEN MAGANG / PELATIHAN
    public function magang()
    {
        if (!$this->isAdmin()) {
            return redirect()
                ->route("login")
                ->with("error", "Silakan login sebagai admin.");
        }
        $magangPakets = Magang::all();
        return view("admin.magang", compact("magangPakets"));
    }

    public function storeMagang(Request $request)
    {
        if (!$this->isAdmin()) {
            return redirect()
                ->route("login")
                ->with("error", "Silakan login sebagai admin.");
        }
        $validated = $request->validate([
            "title" => "required|string|max:255",
            "description" => "required|string",
            "price" => "required|numeric|min:0",
            "image" => "required|image|mimes:jpeg,png,jpg,gif|max:2048",
        ]);

        if ($request->hasFile("image")) {
            $uploadedUrl = $this->uploadToCloudinary(
                $request->file("image"),
                "magang"
            );
            if (!$uploadedUrl) {
                return redirect()->back()->withErrors(['image' => 'Gagal mengunggah gambar ke Cloudinary.'])->withInput();
            }
            $validated["image"] = $uploadedUrl;
        }

        Magang::create($validated);

        return redirect()
            ->route("admin.magang")
            ->with("success", "Paket Magang berhasil ditambahkan.");
    }

    public function updateMagang(Request $request, $id)
    {
        if (!$this->isAdmin()) {
            return redirect()
                ->route("login")
                ->with("error", "Silakan login sebagai admin.");
        }
        $magang = Magang::findOrFail($id);

        $validated = $request->validate([
            "title" => "required|string|max:255",
            "description" => "required|string",
            "price" => "required|numeric|min:0",
            "image" => "nullable|image|mimes:jpeg,png,jpg,gif|max:2048",
        ]);

        if ($request->hasFile("image")) {
            $uploadedUrl = $this->uploadToCloudinary(
                $request->file("image"),
                "magang"
            );
            if (!$uploadedUrl) {
                return redirect()->back()->withErrors(['image' => 'Gagal mengunggah gambar baru ke Cloudinary.'])->withInput();
            }
            $this->deleteFromCloudinary($magang->image);
            $validated["image"] = $uploadedUrl;
        }

        $magang->update($validated);

        return redirect()
            ->route("admin.magang")
            ->with("success", "Paket Magang berhasil diperbarui.");
    }

    public function destroyMagang($id)
    {
        if (!$this->isAdmin()) {
            return redirect()
                ->route("login")
                ->with("error", "Silakan login sebagai admin.");
        }
        $magang = Magang::findOrFail($id);
        $this->deleteFromCloudinary($magang->image);
        $magang->delete();

        return redirect()
            ->route("admin.magang")
            ->with("success", "Paket Magang berhasil dihapus.");
    }

    // HELPER CLOUDINARY
    private function deleteFromCloudinary($url)
    {
        if (!$url) {
            return false;
        }

        // Contoh URL: https://res.cloudinary.com/demo/image/upload/v12345678/products/abcde.jpg
        // Kita butuh: "products/abcde" (tanpa ekstensi)
        $pathString = parse_url($url, PHP_URL_PATH);
        if (!$pathString) {
            return false;
        }

        // Ambil bagian setelah /upload/vxxxxxxxxx/ atau /upload/
        if (preg_match('/\/upload\/(?:v\d+\/)?(.+)$/', $pathString, $matches)) {
            $publicIdWithExt = $matches[1];
            // Hapus ekstensi file (.jpg, .png, dll)
            $publicId = pathinfo($publicIdWithExt, PATHINFO_FILENAME);
            // Jika ada folder bawaan dari regex (misal: products/abcde), pathinfo hanya mengambil "abcde".
            // Kita perlu mempertahankan nama foldernya.
            $dir = pathinfo($publicIdWithExt, PATHINFO_DIRNAME);
            if ($dir && $dir !== ".") {
                $publicId = $dir . "/" . $publicId;
            }

            try {
                cloudinary()->destroy($publicId);
                return true;
            } catch (\Exception $e) {
                return false;
            }
        }

        return false;
    }

    private function uploadToCloudinary($file, $folder)
    {
        try {
            $result = cloudinary()->upload($file->getRealPath(), ['folder' => $folder]);

            if (!$result) {
                return null;
            }

            return $result?->getSecurePath()
                ?? ($result?->getResponse()['secure_url'] ?? null);

        } catch (\Exception $e) {
            // Melacak pesan error jika ada masalah konfigurasi di Railway
            \Illuminate\Support\Facades\Log::error('Cloudinary Upload Error: ' . $e->getMessage());
            return null;
        }
    }

    // MANAJEMEN JADWAL BUKA/TUTUP
    public function jadwal()
    {
        if (!$this->isAdmin()) {
            return redirect()
                ->route("login")
                ->with("error", "Silakan login sebagai admin.");
        }
        // Ambil jadwal seminggu penuh (Senin-Minggu) atau yang ada di DB
        $jadwals = Jadwal::orderBy("id", "asc")->get();
        return view("admin.jadwal", compact("jadwals"));
    }

    public function updateJadwal(Request $request, $id)
    {
        if (!$this->isAdmin()) {
            return redirect()
                ->route("login")
                ->with("error", "Silakan login sebagai admin.");
        }
        $jadwal = Jadwal::findOrFail($id);

        $request->validate([
            "jam_buka" => "nullable|date_format:H:i",
            "jam_tutup" => "nullable|date_format:H:i",
            "is_tutup" => "required|boolean",
        ]);

        $jadwal->update([
            "jam_buka" => $request->is_tutup ? null : $request->jam_buka,
            "jam_tutup" => $request->is_tutup ? null : $request->jam_tutup,
            "is_tutup" => $request->is_tutup,
        ]);

        return redirect()
            ->route("admin.jadwal")
            ->with("success", "Jadwal hari " . $jadwal->hari . " berhasil diperbarui.");
    }

    // RIWAYAT TRANSAKSI TOKO
    public function transaksiToko(Request $request)
    {
        if (!$this->isAdmin()) {
            return redirect()
                ->route("login")
                ->with("error", "Silakan login sebagai admin.");
        }

        $query = Order::with(["user", "items.product"])->orderBy("created_at", "desc");

        if ($request->filled("status")) {
            $query->where("status", $request->status);
        }

        $orders = $query->get();
        return view("admin.transaksi_toko", compact("orders"));
    }

    public function detailTransaksiToko($id)
    {
        if (!$this->isAdmin()) {
            return redirect()
                ->route("login")
                ->with("error", "Silakan login sebagai admin.");
        }
        $order = Order::with(["user", "items.product"])->findOrFail($id);
        return view("admin.transaksi_toko_detail", compact("order"));
    }

    public function updateStatusTransaksiToko(Request $request, $id)
    {
        if (!$this->isAdmin()) {
            return redirect()
                ->route("login")
                ->with("error", "Silakan login sebagai admin.");
        }
        $order = Order::findOrFail($id);

        $request->validate([
            "status" => "required|string",
            "resi" => "nullable|string|max:255",
        ]);

        $oldStatus = $order->status;
        $newStatus = $request->status;

        $updateData = ["status" => $newStatus];
        if ($request->filled("resi")) {
            $updateData["resi"] = $request->resi;
        }

        // Validasi: Jika status berubah ke Selesai atau Dikirim, pastikan stok mencukupi JIKA pengurangan belum dilakukan di awal.
        // Berdasarkan skema umum midtrans (di PaymentController), stok biasanya berkurang saat 'settlement' (Sudah Dibayar).
        // Jadi di sini kita update status saja secara administratif.
        if ($newStatus === "Dibatalkan Admin" && $oldStatus !== "Dibatalkan Admin" && $oldStatus !== "Dibatalkan Pengguna" && $oldStatus !== "Expired") {
            // Jika dibatalkan oleh admin, kembalikan stok produk jika status sebelumnya sudah dibayar/proses
            if (in_array($oldStatus, ["Sudah Dibayar", "Diproses", "Dikirim"])) {
                foreach ($order->items as $item) {
                    if ($item->product) {
                        $item->product->increment("stock", $item->quantity);
                    }
                }
            }
        }

        $order->update($updateData);

        return redirect()
            ->route("admin.transaksi_toko.detail", $id)
            ->with("success", "Status transaksi berhasil diperbarui menjadi: " . $newStatus);
    }

    // RIWAYAT PENDAFTARAN KUNJUNGAN
    public function pendaftaranKunjungan(Request $request)
    {
        if (!$this->isAdmin()) {
            return redirect()
                ->route("login")
                ->with("error", "Silakan login sebagai admin.");
        }

        $query = DB::table("pendaftaran_kunjungan")
            ->join("users", "pendaftaran_kunjungan.id_user", "=", "users.id")
            ->join("kunjungan", "pendaftaran_kunjungan.id_kunjungan", "=", "kunjungan.id")
            ->select("pendaftaran_kunjungan.*", "users.name as user_name", "users.email as user_email", "kunjungan.title as paket_title")
            ->orderBy("pendaftaran_kunjungan.created_at", "desc");

        if ($request->filled("status")) {
            $query->where("pendaftaran_kunjungan.status_pembayaran", $request->status);
        }

        $pendaftarans = $query->get();
        return view("admin.pendaftaran_kunjungan", compact("pendaftarans"));
    }

    public function updateStatusKunjungan(Request $request, $id)
    {
        if (!$this->isAdmin()) {
            return redirect()
                ->route("login")
                ->with("error", "Silakan login sebagai admin.");
        }
        $request->validate([
            "status_pembayaran" => "required|string",
        ]);

        DB::table("pendaftaran_kunjungan")
            ->where("id", $id)
            ->update([
                "status_pembayaran" => $request->status_pembayaran,
                "updated_at" => now(),
            ]);

        return redirect()
            ->back()
            ->with("success", "Status pendaftaran kunjungan berhasil diperbarui.");
    }

    // RIWAYAT PENDAFTARAN MAGANG
    public function pendaftaranMagang(Request $request)
    {
        if (!$this->isAdmin()) {
            return redirect()
                ->route("login")
                ->with("error", "Silakan login sebagai admin.");
        }

        $query = DB::table("pendaftaran_magang")
            ->join("users", "pendaftaran_magang.id_user", "=", "users.id")
            ->join("magang", "pendaftaran_magang.id_magang", "=", "magang.id")
            ->select("pendaftaran_magang.*", "users.name as user_name", "users.email as user_email", "magang.title as paket_title")
            ->orderBy("pendaftaran_magang.created_at", "desc");

        if ($request->filled("status")) {
            $query->where("pendaftaran_magang.status_pembayaran", $request->status);
        }

        $pendaftarans = $query->get();
        return view("admin.pendaftaran_magang", compact("pendaftarans"));
    }

    public function updateStatusMagang(Request $request, $id)
    {
        if (!$this->isAdmin()) {
            return redirect()
                ->route("login")
                ->with("error", "Silakan login sebagai admin.");
        }
        $request->validate([
            "status_pembayaran" => "required|string",
        ]);

        DB::table("pendaftaran_magang")
            ->where("id", $id)
            ->update([
                "status_pembayaran" => $request->status_pembayaran,
                "updated_at" => now(),
            ]);

        return redirect()
            ->back()
            ->with("success", "Status pendaftaran magang/pelatihan berhasil diperbarui.");
    }

    // MANAJEMEN SETTING APLIKASI
    public function settings()
    {
        if (!$this->isAdmin()) {
            return redirect()
                ->route("login")
                ->with("error", "Silakan login sebagai admin.");
        }
        $settings = Setting::pluck("value", "key")->toArray();
        return view("admin.settings", compact("settings"));
    }

    public function updateSettings(Request $request)
    {
        if (!$this->isAdmin()) {
            return redirect()
                ->route("login")
                ->with("error", "Silakan login sebagai admin.");
        }
        $data = $request->except("_token");

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(["key" => $key], ["value" => $value]);
        }

        return redirect()
            ->route("admin.settings")
            ->with("success", "Pengaturan aplikasi berhasil diperbarui.");
    }

    // ==========================================
    // FITUR TRANSAKSI OFFLINE (KASIR / LANGSUNG)
    // ==========================================

    public function transaksiOfflineIndex()
    {
        if (!$this->isAdmin()) {
            return redirect()->route("login")->with("error", "Silakan login sebagai admin.");
        }

        $products = Product::where('stock', '>', 0)->get();
        $kunjunganPakets = Kunjungan::all();
        $magangPakets = Magang::all();

        return view('admin.transaksi_offline', compact('products', 'kunjunganPakets', 'magangPakets'));
    }

    public function storeTransaksiOfflineToko(Request $request)
    {
        if (!$this->isAdmin()) {
            return response()->json(["success" => false, "message" => "Unauthorized"], 403);
        }

        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'metode_pembayaran' => 'required|string',
            'nama_pembeli' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            // Buat atau gunakan user dummy khusus offline/kasir jika diperlukan, atau kosongkan id_user (pastikan nullable di DB)
            // Di sini kita buat order dengan user_id = null atau user yang sedang login (admin) sebagai penanggung jawab.
            $order = Order::create([
                'user_id' => Auth::id(), // Admin yang melayani
                'order_id' => 'OFFLINE-' . strtoupper(\Illuminate\Support\Str::random(10)),
                'total_price' => 0,
                'status' => 'Selesai', // Langsung selesai karena pembayaran offline langsung lunas
                'payment_type' => $request->metode_pembayaran,
                'nama_penerima' => $request->nama_pembeli ?? 'Pembeli Offline',
                'is_offline' => 1,
                'catatan' => 'Transaksi Kasir Langsung',
            ]);

            $totalPrice = 0;

            foreach ($request->items as $itemData) {
                $product = Product::lockForUpdate()->find($itemData['product_id']);

                if ($product->stock < $itemData['quantity']) {
                    throw new \Exception("Stok untuk produk {$product->name} tidak mencukupi!");
                }

                $subtotal = $product->price * $itemData['quantity'];
                $totalPrice += $subtotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $itemData['quantity'],
                    'price' => $product->price,
                ]);

                // Kurangi stok
                $product->decrement('stock', $itemData['quantity']);
            }

            // Update total harga riil
            $order->update(['total_price' => $totalPrice]);

            DB::commit();
            return response()->json(["success" => true, "message" => "Transaksi toko offline berhasil dicatat!"]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(["success" => false, "message" => "Terjadi kesalahan: " . $e->getMessage()]);
        }
    }

    public function storeTransaksiOfflineKunjungan(Request $request)
    {
        if (!$this->isAdmin()) {
            return response()->json(["success" => false, "message" => "Unauthorized"], 403);
        }

        $request->validate([
            'id_kunjungan' => 'required|exists:kunjungan,id',
            'email_atau_wa' => 'required|string',
            'nama_instansi' => 'required|string|max:255',
            'jumlah_peserta' => 'required|integer|min:1',
            'tanggal_kunjungan' => 'required|date|after_or_equal:today',
            'metode_pembayaran' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $paket = Kunjungan::find($request->id_kunjungan);

            // Cari user berdasarkan email/wa, jika tidak ada buat akun otomatis agar relasi tetap terjaga
            $user = User::where('email', $request->email_atau_wa)->orWhere('nohp', $request->email_atau_wa)->first();
            if (!$user) {
                $isEmail = filter_var($request->email_atau_wa, FILTER_VALIDATE_EMAIL);
                $user = User::create([
                    'name' => $request->nama_instansi,
                    'email' => $isEmail ? $request->email_atau_wa : 'offline_' . time() . '@example.com',
                    'password' => bcrypt(\Illuminate\Support\Str::random(10)),
                    'nohp' => !$isEmail ? $request->email_atau_wa : null,
                    'alamat' => $request->nama_instansi,
                ]);
            }

            $total_harga = $paket->price * $request->jumlah_peserta;

            DB::table('pendaftaran_kunjungan')->insert([
                'id_user' => $user->id,
                'id_kunjungan' => $paket->id,
                'nama_instansi' => $request->nama_instansi,
                'jumlah_peserta' => $request->jumlah_peserta,
                'tanggal_kunjungan' => $request->tanggal_kunjungan,
                'metode_pembayaran' => $request->metode_pembayaran,
                'status_pembayaran' => 'Diterima', // Langsung diterima
                'total_harga' => $total_harga,
                'is_offline' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();
            return response()->json(["success" => true, "message" => "Transaksi pendaftaran kunjungan offline berhasil dicatat!"]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(["success" => false, "message" => "Terjadi kesalahan: " . $e->getMessage()]);
        }
    }

    public function storeTransaksiOfflineMagang(Request $request)
    {
        if (!$this->isAdmin()) {
            return response()->json(["success" => false, "message" => "Unauthorized"], 403);
        }

        $request->validate([
            'id_magang' => 'required|exists:magang,id',
            'email_atau_wa' => 'required|string',
            'nama_peserta' => 'required|string|max:255',
            'instansi' => 'required|string|max:255',
            'durasi_magang' => 'required|integer|min:1',
            'tanggal_magang' => 'required|date|after_or_equal:today',
            'metode_pembayaran' => 'required|string',
            'no_wa' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $paket = Magang::find($request->id_magang);

            $user = User::where('email', $request->email_atau_wa)->first();
            if (!$user && $request->filled('no_wa')) {
                $user = User::where('nohp', $request->no_wa)->first();
            }

            if (!$user) {
                $isEmail = filter_var($request->email_atau_wa, FILTER_VALIDATE_EMAIL);
                $user = User::create([
                    'name' => $request->nama_peserta,
                    'email' => $isEmail ? $request->email_atau_wa : 'offline_magang_' . time() . '@example.com',
                    'password' => bcrypt(\Illuminate\Support\Str::random(10)),
                    'nohp' => $request->no_wa,
                    'alamat' => $request->instansi,
                ]);
            } else {
                if ($request->filled('no_wa')) $user->update(['nohp' => $request->no_wa]);
                $user->update(['alamat' => $request->instansi]);
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
            return response()->json(["success" => false, "message" => "Terjadi kesalahan: " . $e->getMessage()]);
        }
    }
}
