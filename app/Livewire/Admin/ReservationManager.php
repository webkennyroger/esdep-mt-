<?php

namespace App\Livewire\Admin;

use App\Models\Reservation;
use App\Models\Contact;
use App\Models\ContactGroup;
use App\Actions\ApproveReservationAction;
use App\Actions\RejectReservationAction;
use App\Actions\SendEventAnnouncementAction;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class ReservationManager extends Component
{
    use WithPagination;

    public string $activeFilter = 'all';
    public string $search = '';
    public bool $showApproveModal = false;
    public bool $showRejectModal = false;
    public int $selectedId = 0;
    public string $selectedName = '';
    public string $rejectReason = '';

    public bool $showDetailModal = false;
    public ?Reservation $detailReservation = null;

    public bool $showAnnounceModal = false;
    public int $announceReservationId = 0;
    public string $announceReservationName = '';
    public string $announceMessage = '';
    public string $announceRecipientType = 'all';
    public array $announceSelectedGroupIds = [];
    public array $announceSelectedContactIds = [];
    public $announceGroups = [];
    public $announceContacts = [];

    protected $listeners = [
        'reservationCreated' => '$refresh',
        'reservationUpdated' => '$refresh',
        'reservationApproved' => '$refresh',
        'reservationRejected' => '$refresh',
    ];

    public function setFilter(string $status): void
    {
        $this->activeFilter = $status;
        $this->resetPage();
    }

    public function openDetailModal(int $id): void
    {
        $this->detailReservation = Reservation::with('room')->find($id);
        $this->showDetailModal = true;
    }

    public function closeDetailModal(): void
    {
        $this->showDetailModal = false;
        $this->detailReservation = null;
    }

    public function openApproveModal(int $id, string $name): void
    {
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

    public function confirmApprove(): void
    {
        try {
            $reservation = Reservation::findOrFail($this->selectedId);
            $action = new ApproveReservationAction();
            $action->execute($reservation);
            session()->flash('success', 'Reserva autorizada com sucesso.');
            $this->dispatch('reservationApproved');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }

        $this->closeApproveModal();
    }

    public function openRejectModal(int $id, string $name): void
    {
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

    public function confirmReject(): void
    {
        try {
            $reservation = Reservation::findOrFail($this->selectedId);
            $action = new RejectReservationAction();
            $action->execute($reservation, $this->rejectReason ?: null);
            session()->flash('success', 'Reserva rejeitada com sucesso.');
            $this->dispatch('reservationRejected');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }

        $this->closeRejectModal();
    }

    public function reopenReservation(int $id): void
    {
        try {
            $reservation = Reservation::findOrFail($id);
            $reservation->status = \App\Enums\ReservationStatus::PENDING->value;
            $reservation->reject_reason = null;
            $reservation->save();
            session()->flash('success', 'Reserva reaberta como pendente.');
            $this->dispatch('reservationRejected');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function openAnnounceModal(int $id, string $name): void
    {
        $this->announceReservationId = $id;
        $this->announceReservationName = $name;
        $this->announceMessage = '';
        $this->announceRecipientType = 'all';
        $this->announceSelectedGroupIds = [];
        $this->announceSelectedContactIds = [];
        $this->announceGroups = ContactGroup::withCount('contacts')->get();
        $this->announceContacts = Contact::active()->orderBy('name')->get();
        $this->showAnnounceModal = true;
    }

    public function closeAnnounceModal(): void
    {
        $this->showAnnounceModal = false;
        $this->announceReservationId = 0;
        $this->announceReservationName = '';
    }

    public function sendAnnouncement(): void
    {
        $this->validate([
            'announceRecipientType' => 'required|in:all,groups,contacts',
            'announceSelectedGroupIds' => 'required_if:announceRecipientType,groups|array',
            'announceSelectedContactIds' => 'required_if:announceRecipientType,contacts|array',
        ]);

        try {
            $reservation = Reservation::findOrFail($this->announceReservationId);

            $action = new SendEventAnnouncementAction();
            $announcement = $action->execute($reservation, [
                'message' => $this->announceMessage,
                'recipient_type' => $this->announceRecipientType,
                'group_ids' => $this->announceRecipientType === 'groups' ? $this->announceSelectedGroupIds : [],
                'contact_ids' => $this->announceRecipientType === 'contacts' ? $this->announceSelectedContactIds : [],
            ], auth()->user());

            session()->flash('success', "Evento divulgado com sucesso! ({$announcement->total_recipients} destinatarios)");
            $this->closeAnnounceModal();
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $query = Reservation::with('room')->latest();

        if ($this->activeFilter !== 'all') {
            $query->where('status', $this->activeFilter);
        }

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('event_name', 'like', "%{$this->search}%")
                  ->orWhere('requester_name', 'like', "%{$this->search}%");
            });
        }

        return view('livewire.admin.reservation-manager', [
            'reservations' => $query->paginate(5),
        ]);
    }
}
