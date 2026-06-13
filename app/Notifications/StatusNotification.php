<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class StatusNotification extends Notification
{
    protected $data;
    protected $tipe;
    protected $status;

    public function __construct($tipe, $data, $status)
    {
        $this->tipe   = $tipe;
        $this->data   = $data;
        $this->status = $status;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $config = $this->getConfig();

        $mail = (new MailMessage)
            ->subject($config['subject'])
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line($config['line'])
            ->line('**Detail ' . $config['label'] . ':**');

        foreach ($config['details'] as $detail) {
            $mail->line($detail);
        }

        // Tambahkan baris ekstra jika ada (misal: info pembayaran magang)
        if (!empty($config['extra_lines'])) {
            $mail->line('---');
            foreach ($config['extra_lines'] as $extra) {
                $mail->line($extra);
            }
        }

        return $mail
            ->action('Lihat Detail', $config['url'])
            ->line('Terima kasih telah menggunakan layanan kami.');
    }

    private function getConfig(): array
    {
        $icon = match($this->status) {
            'Diterima'             => '✅',
            'Tidak Diterima'       => '❌',
            'Dibatalkan'           => '🚫',
            'Selesai'              => '🎉',
            'Menunggu Konfirmasi'  => '⏳',
            'Diproses'             => '🔄',
            'Sedang Dikemas'       => '📦',
            'Dikirim'              => '🚚',
            'Terkonfirmasi'        => '✅',
            'Pesanan Siap Diambil' => '🏪',
            default                => '📋',
        };

        $labelStatus = $this->status;

        $label = match($this->tipe) {
            'kunjungan' => 'Reservasi Kunjungan',
            'pesanan'   => 'Pesanan',
            'magang'    => 'Pendaftaran Magang',
            default     => 'Pengajuan',
        };

        $details     = $this->buildDetails();
        $extraLines  = $this->buildExtraLines();
        $url         = $this->buildUrl();

        return [
            'subject'     => "{$icon} Update {$label} - {$labelStatus}",
            'line'        => "Status {$label} kamu sekarang: **{$labelStatus}**",
            'label'       => $label,
            'details'     => $details,
            'extra_lines' => $extraLines,
            'url'         => $url,
        ];
    }

    private function buildDetails(): array
    {
        // Alamat & link GMaps lokasi (digunakan untuk kunjungan & pesanan ambil)
        $alamatLokasi = 'Dusun Krajan 2 RT 015/003 Desa Tanjungsari Timur Kecamatan Cikaum Kabupaten Subang Provinsi Jawa Barat  '; // TODO: sesuaikan alamat lokasi kamu
        $gmapsLokasi  = 'https://maps.app.goo.gl/rqLdhLqh84HSX1xF8'; // TODO: ganti koordinat GMaps kamu

        return match($this->tipe) {

            // ================================================================
            // KUNJUNGAN — tambah alamat & link GMaps
            // ================================================================
            'kunjungan' => [
                "📅 Tanggal   : {$this->data->tanggal_reservasi}",
                "👥 Peserta   : {$this->data->jumlah_peserta} orang",
                "🏫 Instansi  : {$this->data->instansi}",
                "📍 Lokasi    : {$alamatLokasi}",
                "🗺️ Google Maps: {$gmapsLokasi}",
            ],

            // ================================================================
            // PESANAN — detail produk, metode pengiriman, total pembayaran
            // ================================================================
            'pesanan' => $this->buildPesananDetails($gmapsLokasi, $alamatLokasi),

            // ================================================================
            // MAGANG — tambah alamat & link GMaps
            // ================================================================
            'magang' => [
                "🏫 Instansi  : {$this->data->pekerjaan}",
                "📅 Tanggal   : {$this->data->tanggal_magang}",
                "⏳ Durasi    : {$this->data->durasi_magang} bulan",
                "📍 Lokasi    : {$alamatLokasi}",
                "🗺️ Google Maps: {$gmapsLokasi}",
            ],

            default => [],
        };
    }

    private function buildPesananDetails(string $gmapsLokasi, string $alamatLokasi): array
    {
        $details = [];

        // Detail produk yang dibeli
        $items = $this->data->items ?? collect([]);
        if (!empty($items) && $items->isNotEmpty()) {
            $details[] = "🛍️ **Produk yang Dibeli:**";
            foreach ($items as $item) {
                $hargaItem = number_format($item->price ?? 0, 0, ',', '.');
                $namaProduk = $item->product->name ?? 'Produk';
                $satuan = $item->product->satuan ?? 'kg';
                $details[] = "   • {$namaProduk} {$item->quantity} {$satuan} — Rp {$hargaItem}";
            }
        }

        // Metode Pengiriman
        $metodePengiriman = $this->data->metode_pengambilan ?? $this->data->jenis_pengiriman ?? null;

        if ($metodePengiriman) {
            $isAmbil = in_array(strtolower($metodePengiriman), ['diambil', 'ambil', 'pickup', 'ambil sendiri']);

            if ($isAmbil) {
                $details[] = "🏪 Metode     : Diambil Sendiri";
                $details[] = "📍 Alamat     : {$alamatLokasi}";
                $details[] = "🗺️ Google Maps: {$gmapsLokasi}";
            } else {
                $details[] = "🚚 Metode     : Dikirim";
            }
        }

        // Total Pembayaran
        $total     = number_format($this->data->grand_total ?? 0, 0, ',', '.');
        $details[] = "💰 Total Bayar : Rp {$total}";
        $statusBayar = $this->data->status_pembayaran ?? $this->data->payment_status ?? null;
        if ($statusBayar) {
            $details[] = "✅ Status Bayar: {$statusBayar}";
        }

        return $details;
    }

    private function buildExtraLines(): array
    {
        if ($this->tipe === 'magang' && $this->status === 'Terkonfirmasi') {
            $hargaPaket = number_format($this->data->paket_harga ?? 0, 0, ',', '.');
            $namaPaket  = $this->data->paket_name ?? 'Paket Magang';

            return [
                "🎉 **Selamat! Pendaftaran magang kamu telah terkonfirmasi.**",
                "💳 Silakan lakukan pembayaran untuk paket **{$namaPaket}**:",
                "💰 Biaya     : **Rp {$hargaPaket}**",
                "🏦 Pembayaran dapat dilakukan melalui metode yang tersedia di platform kami.",
            ];
        }

        if ($this->tipe === 'pesanan' && $this->status === 'Selesai') {
            $metode = $this->data->metode_pengambilan ?? $this->data->jenis_pengiriman ?? '';
            $isAmbil = in_array(strtolower($metode), ['diambil', 'ambil', 'pickup', 'ambil sendiri']);
            if ($isAmbil) {
                return ["🏪 Pesanan telah berhasil diambil. Terima kasih!"];
            } else {
                return ["🚚 Pesanan telah diterima. Terima kasih!"];
            }
        }

        if ($this->status === 'Dibatalkan') {
            return ["🚫 Pengajuan/pesanan kamu telah dibatalkan karena pembayaran tidak valid."];
        }
        return [];
    }

    private function buildUrl(): string
    {
        return match($this->tipe) {
            'kunjungan' => route('reservasi.riwayat'),
            'pesanan'   => route('nazfram.riwayat-pesanan'),
            'magang'    => route('magang.riwayat'),
            default     => url('/'),
        };
    }
}
