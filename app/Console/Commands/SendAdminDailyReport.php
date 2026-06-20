<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class SendAdminDailyReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:daily-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mengirim laporan harian pesanan dan pendaftaran ke WhatsApp Admin via Fonnte';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // 1. Data Pesanan Produk
        $pesananBaruHariIni = DB::table('orders')
            ->whereDate('created_at', Carbon::today())
            ->count();
            
        $perluDiprosesProduk = DB::table('orders')
            ->whereIn('status', ['Menunggu Pembayaran', 'Pending', 'Terkonfirmasi'])
            ->count();

        // 2. Data Pendaftaran Magang
        $magangBaruHariIni = DB::table('pendaftaran_magang')
            ->whereDate('created_at', Carbon::today())
            ->count();
            
        $magangMenungguKonfirmasi = DB::table('pendaftaran_magang')
            ->where('status_pembayaran', 'Menunggu Konfirmasi')
            ->count();

        // 3. Data Reservasi Kunjungan
        $kunjunganBaruHariIni = DB::table('reservasi_kunjungan')
            ->whereDate('created_at', Carbon::today())
            ->count();
            
        $kunjunganMenungguKonfirmasi = DB::table('reservasi_kunjungan')
            ->where('status_pembayaran', 'Menunggu Konfirmasi')
            ->count();

        // 4. Daftar Tunggu Detail (Ambil 5 terbaru yang belum diproses)
        $daftarTungguMagang = DB::table('pendaftaran_magang')
            ->join('users', 'pendaftaran_magang.id_user', '=', 'users.id')
            ->where('pendaftaran_magang.status_pembayaran', 'Menunggu Konfirmasi')
            ->select('pendaftaran_magang.id_pendaftaran', 'users.name', DB::raw("'Magang' as tipe"))
            ->limit(3)
            ->get();

        $daftarTungguKunjungan = DB::table('reservasi_kunjungan')
            ->join('users', 'reservasi_kunjungan.id_user', '=', 'users.id')
            ->where('reservasi_kunjungan.status_pembayaran', 'Menunggu Konfirmasi')
            ->select('reservasi_kunjungan.id_reservasi as id', 'users.name', DB::raw("'Kunjungan' as tipe"))
            ->limit(2)
            ->get();

        // 5. Susun Pesan
        $waktuSekarang = Carbon::now();
        $jam = $waktuSekarang->hour;
        $periode = $jam < 12 ? 'Pagi' : ($jam < 16 ? 'Siang' : 'Malam');
        
        $pesan = "📊 *LAPORAN STATUS NAZ HIDROFARM* 📊\n";
        $pesan .= "*(Update: " . $periode . " - " . $waktuSekarang->locale('id')->translatedFormat('d F Y') . ")*\n\n";

        $pesan .= "🛒 *PESANAN PRODUK*\n";
        $pesan .= "- Pesanan Baru hari ini: *{$pesananBaruHariIni}*\n";
        $pesan .= "- Perlu Dikirim/Diproses: *{$perluDiprosesProduk}*\n\n";

        $pesan .= "🎓 *PENDAFTARAN MAGANG (PKL)*\n";
        $pesan .= "- Pendaftaran Baru hari ini: *{$magangBaruHariIni}*\n";
        $pesan .= "- Menunggu Konfirmasi: *{$magangMenungguKonfirmasi}* ⚠️\n\n";

        $pesan .= "🚐 *RESERVASI KUNJUNGAN*\n";
        $pesan .= "- Reservasi Baru hari ini: *{$kunjunganBaruHariIni}*\n";
        $pesan .= "- Menunggu Konfirmasi: *{$kunjunganMenungguKonfirmasi}* ⚠️\n\n";

        $pesan .= "📑 *DAFTAR TUNGGU (TERBARU):*\n";
        $i = 1;
        foreach ($daftarTungguMagang as $item) {
            $pesan .= $i++ . ". #MAG-{$item->id_pendaftaran} - {$item->name} ({$item->tipe})\n";
        }
        foreach ($daftarTungguKunjungan as $item) {
            $pesan .= $i++ . ". #RES-{$item->id} - {$item->name} ({$item->tipe})\n";
        }

        if ($i == 1) {
            $pesan .= "_Tidak ada daftar tunggu._\n";
        }

        $pesan .= "\n---\n";
        $pesan .= "*Silakan login ke admin untuk memproses data tersebut.*\n";
        $pesan .= url('/');

        // 6. Kirim Via Fonnte
        $this->sendToFonnte($pesan);
        
        $this->info('Laporan harian berhasil dikirim ke WhatsApp Admin.');
    }

    private function sendToFonnte($message)
    {
        $token = env('FONNTE_TOKEN');
        $target = env('WHATSAPP_ADMIN');

        if (!$token || !$target) {
            $this->error('Token Fonnte atau Nomor Admin belum diatur di .env');
            return;
        }

        $response = Http::withHeaders([
            'Authorization' => $token,
        ])->post('https://api.fonnte.com/send', [
            'target' => $target,
            'message' => $message,
            'countryCode' => '62', // optional
        ]);

        return $response->json();
    }
}
