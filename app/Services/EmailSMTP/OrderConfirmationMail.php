<?php

namespace App\Services\EmailSMTP;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;
    public $order;

    /**
    * Create a new message instance.
    */
    public function __construct(Order $order)
    {
        $this->order = $order;
        if(!$order->relationLoaded('items')) {
            $order->load('items');
        }
    }

    /**
    * Build the message.
    */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Xác nhận đơn hàng #' . $this->order->order_code . ' - Sức Khỏe 24h',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.confirmation',
        );
    }
}
