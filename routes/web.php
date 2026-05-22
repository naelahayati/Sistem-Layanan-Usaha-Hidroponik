<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Nazframcontroller;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\PaymentController;

// --- AUTHENTICATION ---
Route::get("/login", [AuthController::class, "login"])->name("login");
Route::post("/login", [AuthController::class, "dologin"])->name("do.login");
Route::get("/register", [AuthController::class, "register"])->name("register");
Route::post("/register", [AuthController::class, "doregister"])->name("do.register");
Route::post("/logout", [AuthController::class, "logout"])->name("logout");

// --- FORGOT PASSWORD ---
Route::post("/forgot-password/send-code", [ForgotPasswordController::class, "sendCode"])->name("password.send-code");
Route::post("/forgot-password/verify-code", [ForgotPasswordController::class, "verifyCode"])->name("password.verify-code");
Route::post("/forgot-password/reset", [ForgotPasswordController::class, "resetPassword"])->name("password.reset");

// --- USER INTERFACE (PUBLIC) ---
Route::get("/", [Nazframcontroller::class, "home"])->name("nazfram.home");
Route::get("Nazfram/profil", [Nazframcontroller::class, "profil"])->name("nazfram.profil");
Route::get("Nazfram/produk", [Nazframcontroller::class, "produk"])->name("nazfram.produk");
Route::get("Nazfram/kunjungan", [Nazframcontroller::class, "kunjungan"])->name("nazfram.kunjungan");
Route::get("Nazfram/pelatihan", [Nazframcontroller::class, "pelatihan"])->name("nazfram.pelatihan");
Route::get("Nazfram/akun", [Nazframcontroller::class, "akun"])->name("nazfram.akun");

Route::post('/midtrans/callback', [Nazframcontroller::class, 'midtransCallback']);


// --- PENDAFTARAN, RESERVASI & SHOPPING (WAJIB LOGIN USER) ---
Route::middleware(['auth', 'cek_pengguna'])->group(function () {

    // Route Kunjungan
    Route::get("Nazfram/reservasi-kunjungan/{id}", [Nazframcontroller::class, "reservasiKunjungan"])->name("nazfram.reservasi-kunjungan");
    Route::get('/riwayat-reservasi', [NazframController::class, 'riwayat'])->name('reservasi.riwayat');
    Route::post('/reservasi/store', [Nazframcontroller::class, 'storeReservasi'])->name('nazfram.reservasi.store');
    Route::get('/kunjungan/pembayaran/{id}', [Nazframcontroller::class, 'pembayaranKunjungan'])->name('nazfram.pembayaran_kunjungan');
    Route::post('/kunjungan/pembayaran/konfirmasi/{id}', [Nazframcontroller::class, 'konfirmasiPembayaranKunjungan'])->name('nazfram.pembayaran_kunjungan.konfirmasi');
    Route::post('/kunjungan/pembayaran/batal/{id}', [Nazframcontroller::class, 'batalKunjungan'])->name('nazfram.pembayaran_kunjungan.batal');

    // Route Pendaftaran Magang / Pelatihan
    Route::get('/pendaftaran-pelatihan/{id}', [Nazframcontroller::class, 'formPendaftaran'])->name('nazfram.daftar');
    Route::post('/pendaftaran-pelatihan/simpan', [Nazframcontroller::class, 'store'])->name('nazfram.pelatihan.store');
    Route::post('/pelatihan/daftar/proses', [Nazframcontroller::class, 'store'])->name('nazfram.store-magang');
    Route::get('/pelatihan/pembayaran/{id}', [Nazframcontroller::class, 'pembayaranMagang'])->name('nazfram.pembayaran_magang');
    Route::get('/pelatihan/checkout/{id}', [Nazframcontroller::class, 'checkoutMagang'])->name('nazfram.checkout_magang');
    Route::post('/pelatihan/checkout/proses/{id}', [Nazframcontroller::class, 'processCheckoutMagang'])->name('nazfram.checkout_magang.proses');
    Route::post('/pelatihan/pembayaran/konfirmasi/{id}', [Nazframcontroller::class, 'konfirmasiPembayaranMagang'])->name('nazfram.pembayaran_magang.konfirmasi');
    Route::post('/pelatihan/pembayaran/batal/{id}', [Nazframcontroller::class, 'batalMagang'])->name('nazfram.pembayaran_magang.batal');
    Route::get('/pelatihan/events', [Nazframcontroller::class, 'getPublicEvents'])->name('nazfram.pelatihan.events');
    Route::get('/riwayat-pendaftaran-magang', [Nazframcontroller::class, 'riwayatMagang'])->name('magang.riwayat');

    // --- SHOPPING & TRANSAKSI ---
    Route::get("Nazfram/beli-produk/{id?}", [Nazframcontroller::class, "beliProduk"])->name("nazfram.beli-produk");
    Route::get("Nazfram/keranjang", [Nazframcontroller::class, "keranjang"])->name("nazfram.keranjang");
    Route::get("Nazfram/pesanan", [Nazframcontroller::class, "pesanan"])->name("nazfram.pesanan");
    Route::post("Nazfram/pesanan/proses", [Nazframcontroller::class, "prosesPesanan"])->name("nazfram.pesanan.proses");
    Route::post("Nazfram/keranjang/tambah", [Nazframcontroller::class, "tambahKeKeranjang"])->name("nazfram.keranjang.tambah");
    Route::patch("Nazfram/keranjang/update", [Nazframcontroller::class, "updateKeranjang"])->name("nazfram.keranjang.update");
    Route::delete("Nazfram/keranjang/hapus", [Nazframcontroller::class, "hapusKeranjang"])->name("nazfram.keranjang.hapus");

    // --- USER PROFILE ---
    Route::get("Nazfram/profil-saya", [Nazframcontroller::class, "profilSaya"])->name("nazfram.profil-saya");
    Route::post("Nazfram/profil-saya/update", [Nazframcontroller::class, "updateProfilSaya"])->name("nazfram.profil-saya.update");
    Route::post("Nazfram/profil-saya/kirim-kode", [Nazframcontroller::class, "kirimKodeVerifikasiProfil"])->name("nazfram.profil-saya.kirim-kode");
    Route::post("Nazfram/profil-saya/verifikasi-kode", [Nazframcontroller::class, "verifikasiKodeProfil"])->name("nazfram.profil-saya.verifikasi-kode");
    Route::post("Nazfram/profil-saya/ubah-password", [Nazframcontroller::class, "ubahPassword"])->name("nazfram.profil-saya.ubah-password");
    Route::post("Nazfram/profil-saya/ubah-email", [Nazframcontroller::class, "ubahEmail"])->name("nazfram.profil-saya.ubah-email");

    // --- RIWAYAT PESANAN ---
    Route::get("Nazfram/riwayat-pesanan", [Nazframcontroller::class, "riwayatPesanan"])->name("nazfram.riwayat-pesanan");

    // pembayaran kunjungan midtrans
    Route::get('/kunjungan/payment/{id}', [Nazframcontroller::class, 'pembayaranKunjungan'])
    ->name('nazfram.kunjungan.payment');

    // --- PEMBAYARAN QRIS ---
    Route::get("Nazfram/pembayaran/{id}/qr", [PaymentController::class, "generateQR"])
        ->name("nazfram.pembayaran.qr");
    Route::get("Nazfram/pembayaran/{id}", [PaymentController::class, "generateQR"])
        ->name("nazfram.pembayaran");
    Route::post("Nazfram/pembayaran/konfirmasi/{id}", [PaymentController::class, "konfirmasiPembayaran"])
        ->name("nazfram.pembayaran.konfirmasi");
    Route::post("Nazfram/pesanan/expire/{id}", [Nazframcontroller::class, "expirePesanan"])->name("nazfram.pesanan.expire");

}); // <--- PENUTUP UNTUK USER BIASA (CEK_PENGGUNA)

// --- ADMIN AREA (HANYA BUTUH AUTH) ---
Route::middleware(['auth'])->group(function () {

    // --- ADMIN DASHBOARD & LAPORAN ---
    Route::get("/admin", [AdminController::class, "admin"])->name("admin.dashboard");
    Route::get("/admin/laporan", [AdminController::class, "laporan"])->name("admin.laporan");

    // --- MANAJEMEN TRANSAKSI (ADMIN) ---
    Route::get("/admin/transaksi", [AdminController::class, "transaksiAdmin"])->name("admin.transaksi");
    Route::get("/admin/transaksi/get/{id}", [AdminController::class, "getDetailTransaksi"])->name("admin.transaksi.get");
    Route::post("/admin/transaksi/update-status/{id}", [AdminController::class, "updateStatusTransaksi"])->name("admin.transaksi.update-status");

    // --- KELOLA PRODUK (ADMIN) ---
    Route::get("/admin/produk", [AdminController::class, "produkAdmin"])->name("admin.produk-admin");
    Route::get("/admin/produk/create", [AdminController::class, "createProduct"])->name("admin.produk.create");
    Route::post("/admin/produk/add", [AdminController::class, "addProduct"])->name("admin.produk.add");
    Route::get("/admin/produk/edit/{id}", [AdminController::class, "editProductForm"])->name("admin.produk.edit_form");
    Route::post("/admin/produk/edit/{id}", [AdminController::class, "editProduct"])->name("admin.produk.edit");
    Route::delete("/admin/produk/delete/{id}", [AdminController::class, "deleteProduct"])->name("admin.produk.delete");

    // --- KELOLA KUNJUNGAN (ADMIN) ---
    Route::get("/admin/kunjungan", [AdminController::class, "kunjunganAdmin"])->name("admin.kunjungan-admin");
    Route::get("/admin/kunjungan/create", [AdminController::class, "createKunjungan"])->name("admin.kunjungan.create");
    Route::post("/admin/kunjungan/add", [AdminController::class, "addKunjungan"])->name("admin.kunjungan.add");
    Route::get("/admin/kunjungan/edit/{id}", [AdminController::class, "editKunjunganForm"])->name("admin.kunjungan.edit_form");
    Route::post("/admin/kunjungan/edit/{id}", [AdminController::class, "editKunjungan"])->name("admin.kunjungan.edit");
    Route::delete("/admin/kunjungan/delete/{id}", [AdminController::class, "deleteKunjungan"])->name("admin.kunjungan.delete");

    // --- KELOLA MAGANG (ADMIN UNTUK PAKET) ---
    Route::get("/admin/magang", [AdminController::class, "magangAdmin"])->name("admin.magang-admin");
    Route::get("/admin/magang/create", [AdminController::class, "createMagang"])->name("admin.magang.create");
    Route::post("/admin/magang/add", [AdminController::class, "addMagang"])->name("admin.magang.add");
    Route::get("/admin/magang/edit/{id}", [AdminController::class, "editMagangForm"])->name("admin.magang.edit_form");
    Route::post("/admin/magang/edit/{id}", [AdminController::class, "editMagang"])->name("admin.magang.edit");
    Route::delete("/admin/magang/delete/{id}", [AdminController::class, "deleteMagang"])->name("admin.magang.delete");

    // --- MANAJEMEN PENDAFTARAN (KUNJUNGAN & MAGANG) ---
    Route::get("/admin/kunjungan-manajemen", [AdminController::class, "kunjunganManajemen"])->name("admin.kunjungan-manajemen");
    Route::post("/admin/kunjungan/update-status/{id}", [AdminController::class, "updateStatusKunjungan"])->name("admin.kunjungan.update-status");
    Route::get("/admin/magang-manajemen", [AdminController::class, "magangManajemen"])->name("admin.magang-manajemen");
    Route::post("/admin/magang/update-status/{id}", [AdminController::class, "updateStatusMagang"])->name("admin.magang.update-status");

    // --- KELOLA JADWAL (ADMIN) ---
    Route::get("/admin/jadwal", [AdminController::class, "jadwalAdmin"])->name("admin.jadwal-admin");
    Route::get("/admin/jadwal/events", [AdminController::class, "getJadwalEvents"])->name("admin.jadwal.events");
    Route::post("/admin/jadwal/add", [AdminController::class, "addJadwal"])->name("admin.jadwal.add");
    Route::post("/admin/jadwal/edit/{id}", [AdminController::class, "editJadwal"])->name("admin.jadwal.edit");
    Route::delete("/admin/jadwal/delete/{id}", [AdminController::class, "deleteJadwal"])->name("admin.jadwal.delete");
    Route::get("/admin/jadwal/get/{id}", [AdminController::class, "getJadwal"])->name("admin.jadwal.get");

    // --- KELOLA USER & ADMIN ---
    Route::get("/admin/daftar-user", [AdminController::class, "daftarUser"])->name("admin.daftar-user");
    Route::get("/admin/kelola-admin", [AdminController::class, "kelolaAdmin"])->name("admin.kelola-admin");
    Route::get("/admin/admin/create", [AdminController::class, "createAdmin"])->name("admin.admin.create");
    Route::post("/admin/admin/add", [AdminController::class, "addAdmin"])->name("admin.add");
    Route::get("/admin/admin/edit/{id}", [AdminController::class, "editAdminForm"])->name("admin.admin.edit_form");
    Route::post("/admin/admin/edit/{id}", [AdminController::class, "editAdmin"])->name("admin.edit");
    Route::post("/admin/admin/kirim-kode", [AdminController::class, "kirimKodeAdmin"])->name("admin.admin.kirim-kode");
    Route::post("/admin/admin/verifikasi-kode", [AdminController::class, "verifikasiKodeAdmin"])->name("admin.admin.verifikasi-kode");
    Route::post("/admin/admin/ubah-email", [AdminController::class, "ubahEmailAdmin"])->name("admin.admin.ubah-email");
    Route::post("/admin/admin/ubah-password", [AdminController::class, "ubahPasswordAdmin"])->name("admin.admin.ubah-password");
    Route::delete("/admin/admin/delete/{id}", [AdminController::class, "deleteAdmin"])->name("admin.delete");

    // --- PENGATURAN GLOBAL (ADMIN) ---
    Route::get("/admin/settings", [AdminController::class, "settings"])->name("admin.settings");
    Route::post("/admin/settings/update", [AdminController::class, "updateSettings"])->name("admin.settings.update");

});
