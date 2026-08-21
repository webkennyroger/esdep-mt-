<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;

class EventAnnouncementMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Reservation $reservation,
        public string $recipientName,
        public ?string $customMessage = null
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'ESDEP-MT — '.$this->reservation->event_name,
        );
    }

    public function headers(): Headers
    {
        return new Headers(
            text: [
                'List-Unsubscribe: <mailto:'.config('mail.from.address').'?subject=Cancelar%20inscricao>',
                'List-Unsubscribe-Post: List-Unsubscribe=One-Click',
                'Precedence: bulk',
                'X-Mailer: ESDEP-MT/1.0',
                'Return-Path: '.config('mail.from.address'),
                'Sender: '.config('mail.from.address'),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.events.announcement',
            with: [
                'reservation' => $this->reservation,
                'recipientName' => $this->recipientName,
                'customMessage' => $this->customMessage,
            ],
        );
    }
}
