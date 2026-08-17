<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservationRequestedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Reservation $reservation) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Nova Solicitação de Reserva - {$this->reservation->event_name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            html: 'emails.reservation-requested',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
