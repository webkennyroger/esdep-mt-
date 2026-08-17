<?php

namespace App\Actions;

use App\Models\Contact;
use App\Models\EventAnnouncement;
use App\Models\Reservation;
use App\Models\User;
use App\Jobs\SendEventAnnouncementEmailJob;
use Illuminate\Support\Facades\DB;

class SendEventAnnouncementAction
{
    public function execute(Reservation $reservation, array $data, User $creator): EventAnnouncement
    {
        $contacts = $this->resolveContacts($data);

        $announcement = EventAnnouncement::create([
            'reservation_id' => $reservation->id,
            'created_by' => $creator->id,
            'message' => $data['message'] ?? null,
            'recipient_type' => $data['recipient_type'] ?? 'all',
            'total_recipients' => $contacts->count(),
            'sent_count' => 0,
            'failed_count' => 0,
            'status' => 'sending',
        ]);

        foreach ($contacts as $contact) {
            SendEventAnnouncementEmailJob::dispatch(
                $announcement->id,
                $reservation->id,
                $contact->email,
                $contact->name
            );
        }

        return $announcement;
    }

    protected function resolveContacts(array $data): \Illuminate\Support\Collection
    {
        if (!empty($data['contact_ids'])) {
            return Contact::whereIn('id', $data['contact_ids'])->active()->get();
        }

        if (!empty($data['group_ids'])) {
            return Contact::whereHas('groups', function ($query) use ($data) {
                $query->whereIn('contact_groups.id', $data['group_ids']);
            })->active()->get();
        }

        return Contact::active()->get();
    }
}
