<?php

namespace App\Actions;

use App\Events\ReservationUpdated;
use App\Models\Reservation;
use App\Models\Room;
use App\Services\ActivityLogger;
use App\Services\ConflictCheckerService;
use Illuminate\Support\Facades\DB;

class UpdateReservationAction
{
    private ConflictCheckerService $conflictChecker;

    public function __construct(?ConflictCheckerService $conflictChecker = null)
    {
        $this->conflictChecker = $conflictChecker ?? app(ConflictCheckerService::class);
    }

    public function execute(Reservation $reservation, array $data): Reservation
    {
        if (isset($data['status'])) {
            throw new \DomainException('Alteração de status deve utilizar as Actions correspondentes (ApproveReservationAction ou RejectReservationAction).');
        }

        $this->validateChanges($reservation, $data);

        $changes = $reservation->only(array_keys($data));

        DB::transaction(function () use ($reservation, $data) {
            $reservation->update($data);
        });

        DB::afterCommit(function () use ($reservation, $changes) {
            // Registrar no historico de atividades
            ActivityLogger::reservationUpdated($reservation, $changes);

            event(new ReservationUpdated($reservation));
        });

        return $reservation;
    }

    protected function validateChanges(Reservation $reservation, array $data): void
    {
        $room = Room::where('is_active', true)->find($data['room_id'] ?? $reservation->room_id);
        if (! $room) {
            throw new \DomainException('A sala selecionada não está disponível.');
        }

        $attendeesCount = (int) ($data['attendees_count'] ?? $reservation->attendees_count);
        if ($attendeesCount > $room->capacity) {
            throw new \DomainException("O número de participantes excede a capacidade da sala \"{$room->name}\" ({$room->capacity} pessoas).");
        }

        $dateChanged = isset($data['date']) && $data['date'] !== $reservation->date->format('Y-m-d');
        $startTimeChanged = isset($data['start_time']) && $data['start_time'] !== $reservation->start_time;
        $endTimeChanged = isset($data['end_time']) && $data['end_time'] !== $reservation->end_time;
        $roomIdChanged = isset($data['room_id']) && $data['room_id'] !== $reservation->room_id;

        if ($dateChanged || $startTimeChanged || $endTimeChanged || $roomIdChanged) {
            $this->conflictChecker->check(
                $data['room_id'] ?? $reservation->room_id,
                $data['date'] ?? $reservation->date,
                $data['start_time'] ?? $reservation->start_time,
                $data['end_time'] ?? $reservation->end_time,
                $reservation->id
            );
        }
    }
}
