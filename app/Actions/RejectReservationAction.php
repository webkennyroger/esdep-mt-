<?php

namespace App\Actions;

use App\Models\Reservation;
use App\Enums\ReservationStatus;
use App\Mail\ReservationRejectedMail;
use Illuminate\Support\Facades\Mail;

class RejectReservationAction
{
    public function execute(Reservation $reservation, ?string $reason = null): Reservation
    {
        if ($reservation->status === ReservationStatus::REJECTED) {
            throw new \Exception('Esta reserva ja foi rejeitada anteriormente.');
        }

        $reservation->status = ReservationStatus::REJECTED;
        $reservation->reject_reason = $reason;
        $reservation->save();

        event(new \App\Events\ReservationRejected($reservation));

        try {
            Mail::to($reservation->requester_email)->send(new ReservationRejectedMail($reservation));
        } catch (\Exception $e) {
            \Log::error('Erro ao enviar email de rejeicao: ' . $e->getMessage());
        }

        return $reservation;
    }
}
