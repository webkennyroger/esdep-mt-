<?php

namespace App\Mail;

use App\Models\Reservation;
use App\Services\IcsCalendarGenerator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;

class ReservationApprovedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Reservation $reservation) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Reserva Aprovada - {$this->reservation->event_name}",
        );
    }

    public function headers(): Headers
    {
        return new Headers(
            text: [
                'List-Unsubscribe: <mailto:'.config('mail.from.address').'?subject=Cancelar%20inscri%C3%A7%C3%A3o>',
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
            html: 'emails.reservation-approved',
        );
    }

    public function attachments(): array
    {
        $icsGenerator = app(IcsCalendarGenerator::class);
        $icsContent = $icsGenerator->generate($this->reservation);
        $filename = $icsGenerator->getFilename($this->reservation);

        return [
            Attachment::fromData(fn () => $icsContent, $filename)
                ->withMime('text/calendar; charset=utf-8'),
        ];
    }
}
