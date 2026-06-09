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

        return $mail
            ->action('Lihat Detail', $config['url'])
            ->line('Terima kasih telah menggunakan layanan kami.');
    }

    private function getConfig(): array
    {
        $icon = match($this->status) {
            'Diterima'            => '✅',
            'Tidak Diterima'      => '❌',
            'Dibatalkan'          => '🚫',
            'Selesai'             => '🎉',
            'Menunggu Konfirmasi' => '⏳',
            'Diproses'            => '🔄',
            'Sedang Dikemas'      => '📦',
            'Dikirim'             => '🚚',
            'Terkonfirmasi'       => '✅',
            'Pesanan Siap Diambil' => '🏪',
            default               => '📋',
        };

        $labelStatus = $this->status;

        $label = match($this->tipe) {
            'kunjungan' => 'Reservasi Kunjungan',
            'pesanan'   => 'Pesanan',
            'magang'    => 'Pendaftaran Magang',
            default     => 'Pengajuan',
        };

        $details = match($this->tipe) {
            'kunjungan' => [
                "📅 Tanggal  : {$this->data->tanggal_reservasi}",
                "👥 Peserta  : {$this->data->jumlah_peserta} orang",
                "🏫 Instansi : {$this->data->instansi}",
            ],
            'pesanan' => [
                "🛒 ID Pesanan : #{$this->data->id}",
                "💰 Total      : Rp " . number_format($this->data->total_harga ?? 0, 0, ',', '.'),
            ],
            'magang' => [
                "🏫 Instansi : {$this->data->pekerjaan}",
                "📅 Tanggal  : {$this->data->tanggal_magang}",
                "⏳ Durasi   : {$this->data->durasi_magang} bulan",
            ],
            default => [],
        };

        $url = match($this->tipe) {
            'kunjungan' => route('reservasi.riwayat'),
            'pesanan'   => route('nazfram.riwayat-pesanan'),
            'magang'    => route('magang.riwayat'),
            default     => url('/'),
        };

        return [
            'subject' => "{$icon} Update {$label} - {$labelStatus}",
            'line'    => "Status {$label} kamu sekarang: **{$labelStatus}**",
            'label'   => $label,
            'details' => $details,
            'url'     => $url,
        ];
    }
}
