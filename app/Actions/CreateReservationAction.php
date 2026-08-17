<?php

namespace App\Actions;

use App\Models\Room;
use App\Models\Reservation;
use App\Enums\ReservationStatus;
use App\Mail\ReservationRequestedMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class CreateReservationAction
{
    public function execute(array $data): Reservation
    {
        $room = Room::findOrFail($data['room_id']);

        $this->validateConflict($room, $data['date'], $data['start_time'], $data['end_time']);

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
            'status' => ReservationStatus::PENDING->value,
            'coffee_break' => $data['coffee_break'] ?? false,
            'sonorizacao' => $data['sonorizacao'] ?? false,
            'transmissao' => $data['transmissao'] ?? false,
        ]);

        event(new \App\Events\ReservationRequested($reservation));

        try {
            $adminEmail = config('app.admin_email', 'admin@esdep.mt.gov.br');
            Mail::to($adminEmail)->send(new ReservationRequestedMail($reservation));
        } catch (\Exception $e) {
            \Log::error('Erro ao enviar email de notificacao: ' . $e->getMessage());
        }

        return $reservation;
    }

    protected function validateConflict(Room $room, string|Carbon $date, string|Carbon $startTime, string|Carbon $endTime): void
    {
        $conflictChecker = new \App\Services\ConflictCheckerService();
        $conflictChecker->check($room->id, $date, $startTime, $endTime);
    }
}
