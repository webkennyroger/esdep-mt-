<?php

namespace App\Actions;

use App\Enums\ReservationStatus;
use App\Events\ReservationRejected;
use App\Mail\ReservationRejectedMail;
use App\Models\Reservation;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Auth;
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

        // Registrar no historico de atividades
        ActivityLogger::reservationRejected($reservation, $reason);

        $reservation->logs()->create([
            'user_id' => Auth::guard('web')->id(),
            'action' => 'rejected',
            'reason' => $reason ?? 'Reserva rejeitada pelo administrador',
        ]);

        event(new ReservationRejected($reservation));

        try {
            Mail::to($reservation->requester_email)->send(new ReservationRejectedMail($reservation));
        } catch (\Exception $e) {
            \Log::error('Erro ao enviar email de rejeicao: '.$e->getMessage());
        }

        return $reservation;
    }
}
