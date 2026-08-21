<?php

namespace App\Actions;

use App\Events\ReservationUpdated;
use App\Mail\ReservationUpdatedMail;
use App\Models\Reservation;
use App\Services\ActivityLogger;
use App\Services\ConflictCheckerService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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
            throw new \DomainException('Alteracao de status deve utilizar as Actions correspondentes (ApproveReservationAction ou RejectReservationAction).');
        }

        $this->validateChanges($reservation, $data);

        $changes = $reservation->only(array_keys($data));

        DB::transaction(function () use ($reservation, $data) {
            $reservation->update($data);
        });

        DB::afterCommit(function () use ($reservation, $changes) {
            // Registrar no historico de atividades
            ActivityLogger::reservationUpdated($reservation, $changes);

            $reservation->logs()->create([
                'user_id' => Auth::guard('web')->id(),
                'action' => 'updated',
                'changes' => $changes,
                'reason' => Auth::guard('web')->check()
                    ? 'Reserva atualizada pelo administrador'
                    : 'Reserva atualizada pelo contato responsável',
            ]);

            event(new ReservationUpdated($reservation));

            try {
                Mail::to($reservation->requester_email)->send(new ReservationUpdatedMail($reservation));
            } catch (\Exception $e) {
                Log::error('Erro ao enviar email de atualizacao: '.$e->getMessage());
            }
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
