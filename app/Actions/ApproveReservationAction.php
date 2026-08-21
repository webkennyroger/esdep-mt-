<?php

namespace App\Actions;

use App\Enums\ReservationStatus;
use App\Events\ReservationApproved;
use App\Jobs\SyncGoogleCalendarJob;
use App\Models\Reservation;
use App\Services\ActivityLogger;
use App\Services\ConflictCheckerService;
use Illuminate\Support\Facades\Auth;

class ApproveReservationAction
{
    private ConflictCheckerService $conflictChecker;

    public function __construct(?ConflictCheckerService $conflictChecker = null)
    {
        $this->conflictChecker = $conflictChecker ?? app(ConflictCheckerService::class);
    }

    public function execute(Reservation $reservation): Reservation
    {
        if (! in_array($reservation->status, [ReservationStatus::PENDING, ReservationStatus::REJECTED])) {
            throw new \DomainException('Apenas reservas pendentes ou rejeitadas podem ser autorizadas.');
        }

        $this->conflictChecker->check(
            $reservation->room_id,
            $reservation->date,
            $reservation->start_time,
            $reservation->end_time,
            $reservation->id
        );

        $reservation->status = ReservationStatus::AUTHORIZED;
        $reservation->save();

        if (config('services.google_calendar.enabled')) {
            SyncGoogleCalendarJob::dispatch($reservation->id);
        }

        // Registrar no historico de atividades
        ActivityLogger::reservationApproved($reservation);

        $reservation->logs()->create([
            'user_id' => Auth::guard('web')->id(),
            'action' => 'approved',
            'reason' => Auth::guard('web')->check() ? 'Reserva autorizada pelo administrador' : 'Reserva autorizada pelo responsável da sala',
        ]);

        event(new ReservationApproved($reservation));

        return $reservation;
    }
}
