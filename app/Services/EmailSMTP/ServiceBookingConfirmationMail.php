<?php

namespace App\Services\EmailSMTP;

use App\Models\Order;
use App\Models\ServiceBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ServiceBookingConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;
    public $booking;

    /**
    * Create a new message instance.
    */
    public function __construct(ServiceBooking $booking)
    {
        $this->booking = $booking->load('service');
    }

    /**
    * Build the message.
    */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Xác nhận đặt lịch dịch vụ - Pharma24h',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.services.booking_confirmation',
        );
    }
}
