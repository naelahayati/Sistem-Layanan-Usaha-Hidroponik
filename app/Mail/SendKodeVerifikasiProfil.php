<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendKodeVerifikasiProfil extends Mailable
{
    use Queueable, SerializesModels;

    public $code;
    public $jenis; // 'password' atau 'email'

    public function __construct($code, $jenis = 'password')
    {
        $this->code = $code;
        $this->jenis = $jenis;
    }

    public function envelope(): Envelope
    {
        $subject = $this->jenis === 'email'
            ? 'Kode Verifikasi Ubah Email - Naz Hidrofarm'
            : 'Kode Verifikasi Ubah Password - Naz Hidrofarm';

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.kode-verifikasi-profil');
    }

    public function attachments(): array
    {
        return [];
    }
}
