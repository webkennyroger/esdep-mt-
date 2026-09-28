<?php

namespace App\Actions;

use App\Jobs\SendEventAnnouncementJob;
use App\Models\Contact;
use App\Models\EventAnnouncement;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Support\Collection;

class SendEventAnnouncementAction
{
    public function __construct(
        private NotifyReservationStatusChangeAction $notifyStatusChange = new NotifyReservationStatusChangeAction,
    ) {}

    public function execute(Reservation $reservation, array $data, User|Contact $creator): EventAnnouncement
    {
        $this->notifyStatusChange->execute($reservation);
        $contacts = $this->resolveContacts($data);
        $extraEmails = $this->parseExtraEmails($data['extra_emails'] ?? '');
        $total = $contacts->count() + count($extraEmails);

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

        SendEventAnnouncementJob::dispatch($announcement->id, $contacts->pluck('id')->all(), $extraEmails);

        return $announcement;
    }

    /**
     * Permite ao admin avisar e-mails que ainda nao tem cadastro de contato
     * (ex.: um convidado avulso), sem precisar criar um Contact so para isso.
     *
     * @return array<int, string>
     */
    protected function parseExtraEmails(string $extraEmails): array
    {
        return collect(explode(',', $extraEmails))
            ->map(fn (string $email) => trim($email))
            ->filter(fn (string $email) => $email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL))
            ->unique()
            ->values()
            ->all();
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
