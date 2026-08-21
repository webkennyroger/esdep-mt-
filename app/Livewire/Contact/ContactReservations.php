<?php

namespace App\Livewire\Contact;

use App\Actions\ApproveReservationAction;
use App\Actions\RejectReservationAction;
use App\Actions\SendEventAnnouncementAction;
use App\Enums\ReservationStatus;
use App\Exceptions\ConflictException;
use App\Models\Contact;
use App\Models\Reservation;
use App\Services\ConflictCheckerService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.contact', ['title' => 'Gerenciar Reservas — ESDEP-MT'])]
class ContactReservations extends Component
{
    use WithPagination;

    public string $activeFilter = 'all';

    public string $search = '';

    public bool $showApproveModal = false;

    public bool $showRejectModal = false;

    public bool $showDetailModal = false;

    public bool $showAnnounceModal = false;

    public int $selectedId = 0;

    public string $selectedName = '';

    public string $rejectReason = '';

    public ?Reservation $detailReservation = null;

    public string $detailConflictError = '';

    public string $announceReservationName = '';

    public int $announceReservationId = 0;

    public string $announceMessage = '';

    public string $announceRecipientType = 'all';

    public array $announceSelectedGroupIds = [];

    public array $announceSelectedContactIds = [];

    public $announceGroups = [];

    public $announceContacts = [];

    public function setFilter(string $status): void
    {
        $this->activeFilter = $status;
        $this->resetPage();
    }

    public function openDetailModal(int $id): void
    {
        $this->detailReservation = $this->managedReservation($id)->load('room');
        $this->detailConflictError = '';
        if ($this->detailReservation->status === ReservationStatus::PENDING) {
            try {
                app(ConflictCheckerService::class)->check($this->detailReservation->room_id, $this->detailReservation->date, $this->detailReservation->start_time, $this->detailReservation->end_time, $this->detailReservation->id);
            } catch (ConflictException $exception) {
                $this->detailConflictError = $exception->getMessage();
            }
        }
        $this->showDetailModal = true;
    }

    public function closeDetailModal(): void
    {
        $this->showDetailModal = false;
        $this->detailReservation = null;
    }

    public function openApproveModal(int $id, string $name): void
    {
        $this->managedReservation($id);
        $this->showDetailModal = false;
        $this->detailReservation = null;
        $this->selectedId = $id;
        $this->selectedName = $name;
        $this->showApproveModal = true;
    }

    public function closeApproveModal(): void
    {
        $this->showApproveModal = false;
        $this->selectedId = 0;
        $this->selectedName = '';
    }

    public function confirmApprove(?int $id = null): void
    {
        $reservationId = $id ?: $this->selectedId;
        if ($reservationId < 1) {
            $this->closeApproveModal();
            $this->toast('Reserva não identificada. Atualize a página e tente novamente.', 'error');

            return;
        }
        app(ApproveReservationAction::class)->execute($this->managedReservation($reservationId));
        $this->closeApproveModal();
        $this->toast('Reserva autorizada com sucesso.');
    }

    public function approve(int $id): void
    {
        app(ApproveReservationAction::class)->execute($this->managedReservation($id));
        $this->toast('Reserva autorizada com sucesso.');
    }

    public function openRejectModal(int $id, string $name): void
    {
        $this->managedReservation($id);
        $this->selectedId = $id;
        $this->selectedName = $name;
        $this->rejectReason = '';
        $this->showRejectModal = true;
    }

    public function closeRejectModal(): void
    {
        $this->showRejectModal = false;
        $this->selectedId = 0;
        $this->selectedName = '';
        $this->rejectReason = '';
    }

    public function confirmReject(?int $id = null): void
    {
        $reservationId = $id ?: $this->selectedId;
        if ($reservationId < 1) {
            $this->closeRejectModal();
            $this->toast('Reserva não identificada. Atualize a página e tente novamente.', 'error');

            return;
        }
        app(RejectReservationAction::class)->execute($this->managedReservation($reservationId), $this->rejectReason ?: 'Rejeitada pelo responsável da sala');
        $this->closeRejectModal();
        $this->toast('Reserva rejeitada.');
    }

    public function reject(int $id): void
    {
        app(RejectReservationAction::class)->execute($this->managedReservation($id), 'Rejeitada pelo responsável da sala');
        $this->toast('Reserva rejeitada.');
    }

    public function openAnnounceModal(int $id, string $name): void
    {
        $this->managedReservation($id);
        $contact = auth('contact')->user();
        $this->announceReservationId = $id;
        $this->announceReservationName = $name;
        $this->announceMessage = '';
        $this->announceRecipientType = 'all';
        $this->announceSelectedGroupIds = [];
        $this->announceSelectedContactIds = [];
        $this->announceGroups = $contact->groups()->orderBy('name')->get();
        $this->announceContacts = Contact::active()->whereHas('groups', fn ($groups) => $groups->whereIn('contact_groups.id', $contact->groups()->select('contact_groups.id')))->orderBy('name')->get();
        $this->showAnnounceModal = true;
    }

    public function closeAnnounceModal(): void
    {
        $this->showAnnounceModal = false;
        $this->announceReservationId = 0;
    }

    public function sendAnnouncement(): void
    {
        $this->validate([
            'announceRecipientType' => 'required|in:all,groups,contacts',
            'announceSelectedGroupIds' => 'required_if:announceRecipientType,groups|array',
            'announceSelectedContactIds' => 'required_if:announceRecipientType,contacts|array',
        ]);

        $contact = auth('contact')->user();
        $reservation = $this->managedReservation($this->announceReservationId);
        $allowedGroupIds = $contact->groups()->pluck('contact_groups.id');
        $allowedContactIds = Contact::active()->whereHas('groups', fn ($groups) => $groups->whereIn('contact_groups.id', $allowedGroupIds))->pluck('id');
        $groupIds = collect($this->announceSelectedGroupIds)->map(fn ($id) => (int) $id);
        $contactIds = collect($this->announceSelectedContactIds)->map(fn ($id) => (int) $id);
        abort_if($groupIds->diff($allowedGroupIds)->isNotEmpty() || $contactIds->diff($allowedContactIds)->isNotEmpty(), 403);

        $recipients = match ($this->announceRecipientType) {
            'groups' => ['group_ids' => $groupIds->all(), 'contact_ids' => []],
            'contacts' => ['group_ids' => [], 'contact_ids' => $contactIds->all()],
            default => ['group_ids' => [], 'contact_ids' => $allowedContactIds->all()],
        };
        $announcement = app(SendEventAnnouncementAction::class)->execute($reservation, [
            'message' => $this->announceMessage,
            'recipient_type' => $this->announceRecipientType,
            ...$recipients,
        ], $contact);
        $this->closeAnnounceModal();
        $this->toast("Evento divulgado com sucesso para {$announcement->total_recipients} destinatário(s).");
    }

    public function reopenReservation(int $id): void
    {
        DB::transaction(function () use ($id): void {
            $reservation = $this->managedReservation($id);
            app(ConflictCheckerService::class)->check($reservation->room_id, $reservation->date, $reservation->start_time, $reservation->end_time, $reservation->id);
            $reservation->update(['status' => ReservationStatus::PENDING, 'reject_reason' => null]);
        });
        $this->toast('Reserva reaberta como pendente.');
    }

    public function reorderReservations(array $ids): void
    {
        $query = Reservation::whereIn('room_id', auth('contact')->user()->managedRoomIds());
        abort_if((clone $query)->whereIn('id', $ids)->count() !== count(array_unique($ids)), 403);
        foreach ($ids as $position => $id) {
            (clone $query)->whereKey($id)->update(['sort_order' => $position]);
        }
    }

    private function managedReservation(int $id): Reservation
    {
        return Reservation::whereKey($id)->whereIn('room_id', auth('contact')->user()->managedRoomIds())->firstOrFail();
    }

    private function toast(string $message, string $type = 'success'): void
    {
        $detail = json_encode(compact('type', 'message'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
        $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: {$detail} }))");
    }

    public function render()
    {
        $query = Reservation::whereIn('room_id', auth('contact')->user()->managedRoomIds())->with('room')->orderBy('sort_order')->latest('created_at');
        if ($this->activeFilter !== 'all') {
            $query->where('status', $this->activeFilter);
        }
        if ($this->search !== '') {
            $query->where(fn ($q) => $q->where('event_name', 'like', "%{$this->search}%")->orWhere('requester_name', 'like', "%{$this->search}%"));
        }

        return view('livewire.admin.reservation-manager', ['reservations' => $query->paginate(10)]);
    }
}
