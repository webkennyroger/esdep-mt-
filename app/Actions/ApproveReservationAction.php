<?php

namespace App\Actions;

use App\Models\Reservation;
use App\Enums\ReservationStatus;
use App\Mail\ReservationApprovedMail;
use Illuminate\Support\Facades\Mail;

class ApproveReservationAction
{
    public function execute(Reservation $reservation): Reservation
    {
        if (!in_array($reservation->status, [ReservationStatus::PENDING, ReservationStatus::REJECTED])) {
            throw new \Exception('Apenas reservas pendentes ou rejeitadas podem ser autorizadas.');
        }

        $conflictChecker = new \App\Services\ConflictCheckerService();
        $conflictChecker->check(
            $reservation->room_id,
            $reservation->date,
            $reservation->start_time,
            $reservation->end_time,
            $reservation->id
        );

        $reservation->status = ReservationStatus::AUTHORIZED;
        $reservation->save();

        event(new \App\Events\ReservationApproved($reservation));

        try {
            Mail::to($reservation->requester_email)->send(new ReservationApprovedMail($reservation));
        } catch (\Exception $e) {
            \Log::error('Erro ao enviar email de aprovacao: ' . $e->getMessage());
        }

        return $reservation;
    }
}
