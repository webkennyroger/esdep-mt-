<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
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
            subject: 'ESDEP-MT — ' . $this->reservation->event_name,
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
