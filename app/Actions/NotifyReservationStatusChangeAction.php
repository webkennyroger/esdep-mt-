<?php

namespace App\Actions;

use App\Enums\ReservationStatus;
use App\Mail\ReservationApprovedMail;
use App\Mail\ReservationRejectedMail;
use App\Models\Reservation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Avisa o solicitante (com copia para os donos da sala) quando a reserva e
 * autorizada ou rejeitada. Idempotente por status via
 * `status_notification_sent_for`, para nao duplicar o aviso quando o admin
 * tambem aciona "Divulgar Evento" manualmente depois.
 */
class NotifyReservationStatusChangeAction
{
    public function execute(Reservation $reservation): void
    {
        $status = $reservation->status->value;
        if (! in_array($reservation->status, [ReservationStatus::AUTHORIZED, ReservationStatus::REJECTED], true)
            || $reservation->status_notification_sent_for === $status) {
            return;
        }

        try {
            $mail = $reservation->status === ReservationStatus::AUTHORIZED
                ? new ReservationApprovedMail($reservation)
                : new ReservationRejectedMail($reservation);

            $ownerEmails = $reservation->room?->ownerEmails() ?? [];

            Mail::to($reservation->requester_email)->cc($ownerEmails)->send($mail);
            $reservation->update(['status_notification_sent_for' => $status]);
        } catch (\Throwable $exception) {
            Log::error('Erro ao enviar notificação de status da reserva: '.$exception->getMessage());
        }
    }
}
