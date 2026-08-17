<?php

namespace App\Actions;

use App\Models\Reservation;
use App\Mail\ReservationUpdatedMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class UpdateReservationAction
{
    public function execute(Reservation $reservation, array $data): Reservation
    {
        $this->validateChanges($reservation, $data);

        if (isset($data['status'])) {
            throw new \Exception('Alteração de status deve utilizar as Actions correspondentes (ApproveReservationAction ou RejectReservationAction).');
        }

        DB::transaction(function () use ($reservation, $data) {
            $reservation->update($data);
        });

        DB::afterCommit(function () use ($reservation) {
            event(new \App\Events\ReservationUpdated($reservation));

            Mail::to($reservation->requester_email)->send(new ReservationUpdatedMail($reservation));
        });

        return $reservation;
    }

    protected function validateChanges(Reservation $reservation, array $data): void
    {
        $dateChanged = isset($data['date']) && $data['date'] !== $reservation->date->format('Y-m-d');
        $startTimeChanged = isset($data['start_time']) && $data['start_time'] !== $reservation->start_time;
        $endTimeChanged = isset($data['end_time']) && $data['end_time'] !== $reservation->end_time;
        $roomIdChanged = isset($data['room_id']) && $data['room_id'] !== $reservation->room_id;

        if ($dateChanged || $startTimeChanged || $endTimeChanged || $roomIdChanged) {
            $conflictChecker = new \App\Services\ConflictCheckerService();
            $conflictChecker->check(
                $data['room_id'] ?? $reservation->room_id,
                $data['date'] ?? $reservation->date,
                $data['start_time'] ?? $reservation->start_time,
                $data['end_time'] ?? $reservation->end_time,
                $reservation->id
            );
        }
    }
}
