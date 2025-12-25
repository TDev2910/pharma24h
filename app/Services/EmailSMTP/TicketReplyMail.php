<?php

namespace App\Services\EmailSMTP;

use App\Models\SupportTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;
    public $replyContent;

    /**
     * Create a new message instance.
     */
    public function __construct(SupportTicket $ticket, $replyContent)
    {
        $this->ticket = $ticket;
        $this->replyContent = $replyContent;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Phản hồi yêu cầu #' . $this->ticket->ticket_id . ' - Pharma24h Support',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            // Chúng ta sẽ tạo file view này ở Bước 3
            view: 'emails.tickets.reply',
        );
    }
}
