<?php

namespace App\Actions;

use App\Enums\ReservationStatus;
use App\Events\ReservationRequested;
use App\Mail\ReservationRequestedMail;
use App\Models\Reservation;
use App\Models\Room;
use App\Services\ActivityLogger;
use App\Services\ConflictCheckerService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CreateReservationAction
{
    private ConflictCheckerService $conflictChecker;

    public function __construct(?ConflictCheckerService $conflictChecker = null)
    {
        $this->conflictChecker = $conflictChecker ?? app(ConflictCheckerService::class);
    }

    public function execute(array $data, bool $notify = true): Reservation
    {
        $room = Room::where('is_active', true)->findOrFail($data['room_id']);

        $this->conflictChecker->check($room->id, $data['date'], $data['start_time'], $data['end_time']);

        $reservation = Reservation::create([
            'room_id' => $room->id,
            'event_name' => $data['event_name'],
            'description' => $data['description'] ?? null,
            'requester_name' => $data['requester_name'],
            'requester_email' => $data['requester_email'],
            'date' => $data['date'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'attendees_count' => $data['attendees_count'],
            'status' => $data['status'] ?? ReservationStatus::PENDING->value,
            'coffee_break' => $data['coffee_break'] ?? false,
            'sonorizacao' => $data['sonorizacao'] ?? false,
            'transmissao' => $data['transmissao'] ?? false,
            'projetor' => $data['projetor'] ?? false,
            'terms_accepted' => $data['terms_accepted'] ?? false,
            'terms_accepted_at' => $data['terms_accepted_at'] ?? null,
            'attachment_path' => $data['attachment_path'] ?? null,
            'attachment_name' => $data['attachment_name'] ?? null,
        ]);

        $protocol = sprintf('RES-%s-%04d', date('Y'), $reservation->id);
        $reservation->update(['protocol' => $protocol]);

        // Registrar no historico de atividades
        ActivityLogger::reservationCreated($reservation);

        event(new ReservationRequested($reservation));

        if ($notify) {
            try {
                $notifyEmails = $room->ownerEmails() ?: [config('app.admin_email', 'admin@esdep.mt.gov.br')];
                Mail::to($notifyEmails)->send(new ReservationRequestedMail($reservation));
            } catch (\Exception $e) {
                Log::error('Erro ao enviar email de notificação: '.$e->getMessage());
            }
        }

        return $reservation;
    }
}
