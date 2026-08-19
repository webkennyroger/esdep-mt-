<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;

class ReservationRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Reservation $reservation) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Reserva Rejeitada - {$this->reservation->event_name}",
        );
    }

    public function headers(): Headers
    {
        return new Headers(
            text: [
                'Precedence: transactional',
                'X-Mailer: ESDEP-MT/1.0',
                'Return-Path: ' . config('mail.from.address'),
                'Sender: ' . config('mail.from.address'),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            html: 'emails.reservation-rejected',
            with: [
                'reservation' => $this->reservation,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
