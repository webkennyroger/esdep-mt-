<?php

namespace App\Actions;

use App\Enums\ReservationStatus;
use App\Exceptions\ConflictException;
use App\Mail\ReservationApprovedMail;
use App\Models\Reservation;
use App\Services\ActivityLogger;
use App\Services\ConflictCheckerService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ApproveReservationAction
{
    private ConflictCheckerService $conflictChecker;

    public function __construct(?ConflictCheckerService $conflictChecker = null)
    {
        $this->conflictChecker = $conflictChecker ?? app(ConflictCheckerService::class);
    }

    public function execute(Reservation $reservation): Reservation
    {
        if (!in_array($reservation->status, [ReservationStatus::PENDING, ReservationStatus::REJECTED])) {
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

        // Registrar no historico de atividades
        ActivityLogger::reservationApproved($reservation);

        $reservation->logs()->create([
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'action'  => 'approved',
            'reason'  => 'Reserva autorizada pelo administrador',
        ]);

        event(new \App\Events\ReservationApproved($reservation));

        try {
            Mail::to($reservation->requester_email)->send(new ReservationApprovedMail($reservation));
        } catch (\Exception $e) {
            Log::error('Erro ao enviar email de aprovacao: ' . $e->getMessage());
        }

        return $reservation;
    }
}
