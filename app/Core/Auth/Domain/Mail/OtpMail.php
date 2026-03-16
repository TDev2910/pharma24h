<?php

namespace App\Core\Auth\Domain\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $otp
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Mã xác thực OTP của bạn - Pharma24h',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.auth.otp',
        );
    }
}
