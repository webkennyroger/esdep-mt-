<?php

namespace App\Actions;

use App\Enums\ReservationStatus;
use App\Events\ReservationRejected;
use App\Models\Reservation;
use App\Services\ActivityLogger;

class RejectReservationAction
{
    public function execute(Reservation $reservation, ?string $reason = null): Reservation
    {
        if ($reservation->status === ReservationStatus::REJECTED) {
            throw new \Exception('Esta reserva já foi rejeitada anteriormente.');
        }

        $reservation->status = ReservationStatus::REJECTED;
        $reservation->reject_reason = $reason;
        $reservation->save();

        // Registrar no historico de atividades
        ActivityLogger::reservationRejected($reservation, $reason);

        event(new ReservationRejected($reservation));

        return $reservation;
    }
}
