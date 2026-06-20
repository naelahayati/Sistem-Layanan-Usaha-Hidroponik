<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class SendAdminDailyReport extends Command
{
    protected $signature = 'admin:daily-report';
    protected $description = 'Mengirim laporan harian status layanan ke WhatsApp Admin via Fonnte';

    public function handle()
    {
        // 1. Data Pesanan Produk
        $pesananHariIni = DB::table('orders')->whereDate('created_at', Carbon::today())->count();
        $produkStatus = DB::table('orders')
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')->toArray();

        // 2. Data Magang
        $magangHariIni = DB::table('pendaftaran_magang')->whereDate('created_at', Carbon::today())->count();
        $magangStatus = DB::table('pendaftaran_magang')
            ->select('status_pembayaran', DB::raw('count(*) as total'))
            ->groupBy('status_pembayaran')
            ->pluck('total', 'status_pembayaran')->toArray();

        // 3. Data Kunjungan
        $kunjunganHariIni = DB::table('reservasi_kunjungan')->whereDate('created_at', Carbon::today())->count();
        $kunjunganStatus = DB::table('reservasi_kunjungan')
            ->select('status_pembayaran', DB::raw('count(*) as total'))
            ->groupBy('status_pembayaran')
            ->pluck('total', 'status_pembayaran')->toArray();

        // 4. Susun Pesan
        $waktuSekarang = Carbon::now();
        $pesan = "📊 *LAPORAN STATUS NAZ HIDROFARM* 📊\n";
        $pesan .= "*(Update: " . $waktuSekarang->locale('id')->translatedFormat('d F Y, H:i') . " WIB)*\n\n";

        $pesan .= "🛒 *PESANAN PRODUK*\n";
        $pesan .= "- Pesanan Baru hari ini: *{$pesananHariIni}*\n";
        $statusList = ['Menunggu Konfirmasi', 'Diproses', 'Sedang Dikemas', 'Pesanan Siap Diambil', 'dikirim'];
        foreach ($statusList as $status) {
            $count = $produkStatus[$status] ?? 0;
            $pesan .= "- {$status}: {$count}\n";
        }

        $pesan .= "\n🎓 *PENDAFTARAN MAGANG*\n";
        $pesan .= "- Daftar hari ini: *{$magangHariIni}*\n";
        $statusMagang = ['Menunggu Konfirmasi', 'Terkonfirmasi'];
        foreach ($statusMagang as $status) {
            $count = $magangStatus[$status] ?? 0;
            $pesan .= "- {$status}: {$count}\n";
        }

        $pesan .= "\n🚐 *RESERVASI KUNJUNGAN*\n";
        $pesan .= "- Reservasi hari ini: *{$kunjunganHariIni}*\n";
        $statusKunjungan = ['Menunggu Konfirmasi'];
        foreach ($statusKunjungan as $status) {
            $count = $kunjunganStatus[$status] ?? 0;
            $pesan .= "- {$status}: {$count}\n";
        }

        $pesan .= "\n---\n";
        $pesan .= "*Silakan login ke admin untuk memproses data tersebut.*\n";
        $pesan .= url('/');

        $this->sendToFonnte($pesan);
        $this->info('Laporan harian berhasil dikirim.');
    }

    private function sendToFonnte($message)
    {
        $token = env('FONNTE_TOKEN');
        $target = env('WHATSAPP_ADMIN');
        if (!$token || !$target) return;

        Http::withHeaders(['Authorization' => $token])
            ->post('https://api.fonnte.com/send', [
                'target' => $target,
                'message' => $message,
                'countryCode' => '62',
            ]);
    }
}
