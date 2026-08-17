<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservationUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Reservation $reservation) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Reserva Atualizada - {$this->reservation->event_name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            html: 'emails.reservation-updated',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
