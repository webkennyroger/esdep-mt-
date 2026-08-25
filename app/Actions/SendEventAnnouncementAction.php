<?php

namespace App\Actions;

use App\Mail\ReservationApprovedMail;
use App\Mail\ReservationRejectedMail;
use App\Enums\ReservationStatus;
use App\Jobs\SendEventAnnouncementJob;
use App\Models\Contact;
use App\Models\EventAnnouncement;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEventAnnouncementAction
{
    public function execute(Reservation $reservation, array $data, User|Contact $creator): EventAnnouncement
    {
        $this->sendStatusNotification($reservation);
        $contacts = $this->resolveContacts($data);
        $total = $contacts->count();

        $announcement = EventAnnouncement::create([
            'reservation_id' => $reservation->id,
            'created_by' => $creator instanceof User ? $creator->id : null,
            'created_by_contact_id' => $creator instanceof Contact ? $creator->id : null,
            'message' => $data['message'] ?? null,
            'recipient_type' => $data['recipient_type'] ?? 'all',
            'total_recipients' => $total,
            'sent_count' => 0,
            'failed_count' => 0,
            'status' => 'sending',
            'sent_at' => null,
        ]);

        SendEventAnnouncementJob::dispatch($announcement->id, $contacts->pluck('id')->all());

        return $announcement;
    }

    private function sendStatusNotification(Reservation $reservation): void
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
            Log::error('Erro ao enviar notificacao de status da reserva: '.$exception->getMessage());
        }
    }

    protected function resolveContacts(array $data): Collection
    {
        if (! empty($data['contact_ids'])) {
            return Contact::whereIn('id', $data['contact_ids'])->active()->get();
        }

        if (! empty($data['group_ids'])) {
            return Contact::whereHas('groups', function ($query) use ($data) {
                $query->whereIn('contact_groups.id', $data['group_ids']);
            })->active()->get();
        }

        return Contact::query()->whereRaw('1 = 0')->get();
    }
}
