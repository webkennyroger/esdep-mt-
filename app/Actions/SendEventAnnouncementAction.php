<?php

namespace App\Actions;

use App\Mail\EventAnnouncementMail;
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

        foreach ($contacts as $contact) {
            try {
                Mail::to($contact->email)->send(
                    new EventAnnouncementMail($reservation, $contact->name, $announcement->message)
                );

                $announcement->increment('sent_count');
            } catch (\Exception $e) {
                $announcement->increment('failed_count');
                Log::error("Erro ao enviar email para {$contact->email}: ".$e->getMessage());
            }
        }

        $announcement->update([
            'status' => 'completed',
            'sent_at' => now(),
        ]);

        return $announcement;
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

        return Contact::active()->get();
    }
}
