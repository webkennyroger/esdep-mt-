<?php

namespace App\Livewire\Admin;

use App\Actions\ApproveReservationAction;
use App\Actions\RejectReservationAction;
use App\Actions\SendEventAnnouncementAction;
use App\Enums\ReservationStatus;
use App\Exceptions\ConflictException;
use App\Models\Contact;
use App\Models\ContactGroup;
use App\Models\Reservation;
use App\Services\ConflictCheckerService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

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
    public string $detailConflictError = '';

    protected $listeners = [
        'reservationCreated'  => '$refresh',
        'reservationUpdated'  => '$refresh',
        'reservationApproved' => '$refresh',
        'reservationRejected' => '$refresh',
    ];

    // ─── Helpers ─────────────────────────────────────

    /**
     * Escapa uma mensagem de excecao para uso seguro em JS inline via CustomEvent.
     * Evita XSS ao remover tags e escapar aspas simples/duplas.
     */
    private function safeMessage(string $message): string
    {
        return addslashes(strip_tags($message));
    }

    // ─── Filter ──────────────────────────────────────

    public function setFilter(string $status): void
    {
        $this->activeFilter = $status;
        $this->resetPage();
    }

    // ─── Detail Modal ─────────────────────────────────

    public function openDetailModal(int $id): void
    {
        $this->detailReservation = Reservation::with(['room', 'logs.user'])->find($id);
        $this->detailConflictError = '';

        if ($this->detailReservation && $this->detailReservation->status === ReservationStatus::PENDING) {
            try {
                app(ConflictCheckerService::class)->check(
                    $this->detailReservation->room_id,
                    $this->detailReservation->date,
                    $this->detailReservation->start_time,
                    $this->detailReservation->end_time,
                    $this->detailReservation->id
                );
            } catch (ConflictException $e) {
                $this->detailConflictError = $e->getMessage();
            }
        }

        $this->showDetailModal = true;
    }

    public function closeDetailModal(): void
    {
        $this->showDetailModal = false;
        $this->detailReservation = null;
    }

    // ─── Approve Modal ────────────────────────────────

    public function openApproveModal(int $id, string $name): void
    {
        $this->selectedId   = $id;
        $this->selectedName = $name;
        $this->showApproveModal = true;
    }

    public function closeApproveModal(): void
    {
        $this->showApproveModal = false;
        $this->selectedId   = 0;
        $this->selectedName = '';
    }

    public function confirmApprove(): void
    {
        try {
            $reservation = Reservation::findOrFail($this->selectedId);
            app(ApproveReservationAction::class)->execute($reservation);
            $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: 'Reserva autorizada com sucesso.' } }))");
            $this->dispatch('reservationApproved');
        } catch (\Exception $e) {
            $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: '" . $this->safeMessage($e->getMessage()) . "' } }))");
        }

        $this->closeApproveModal();
    }

    // ─── Reject Modal ─────────────────────────────────

    public function openRejectModal(int $id, string $name): void
    {
        $this->selectedId   = $id;
        $this->selectedName = $name;
        $this->rejectReason = '';
        $this->showRejectModal = true;
    }

    public function closeRejectModal(): void
    {
        $this->showRejectModal = false;
        $this->selectedId   = 0;
        $this->selectedName = '';
        $this->rejectReason = '';
    }

    public function confirmReject(): void
    {
        try {
            $reservation = Reservation::findOrFail($this->selectedId);
            app(RejectReservationAction::class)->execute($reservation, $this->rejectReason ?: null);
            $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: 'Reserva rejeitada com sucesso.' } }))");
            $this->dispatch('reservationRejected');
        } catch (\Exception $e) {
            $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: '" . $this->safeMessage($e->getMessage()) . "' } }))");
        }

        $this->closeRejectModal();
    }

    // ─── Reopen ───────────────────────────────────────

    public function reopenReservation(int $id): void
    {
        try {
            DB::transaction(function () use ($id) {
                $reservation = Reservation::findOrFail($id);

                // Verifica conflito antes de reabrir
                app(ConflictCheckerService::class)->check(
                    $reservation->room_id,
                    $reservation->date,
                    $reservation->start_time,
                    $reservation->end_time,
                    $reservation->id
                );

                $reservation->status        = ReservationStatus::PENDING;
                $reservation->reject_reason = null;
                $reservation->save();
            });

            $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: 'Reserva reaberta como pendente.' } }))");
            $this->dispatch('reservationRejected');
        } catch (\Exception $e) {
            $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: '" . $this->safeMessage($e->getMessage()) . "' } }))");
        }
    }

    // ─── Announce Modal ───────────────────────────────

    public function openAnnounceModal(int $id, string $name): void
    {
        $this->announceReservationId       = $id;
        $this->announceReservationName     = $name;
        $this->announceMessage             = '';
        $this->announceRecipientType       = 'all';
        $this->announceSelectedGroupIds    = [];
        $this->announceSelectedContactIds  = [];
        $this->announceGroups              = ContactGroup::orderBy('name')->get();
        $this->announceContacts            = Contact::active()->orderBy('name')->get();
        $this->showAnnounceModal           = true;
    }

    public function closeAnnounceModal(): void
    {
        $this->showAnnounceModal       = false;
        $this->announceReservationId   = 0;
        $this->announceReservationName = '';
    }

    public function sendAnnouncement(): void
    {
        $this->validate([
            'announceRecipientType'        => 'required|in:all,groups,contacts',
            'announceSelectedGroupIds'     => 'required_if:announceRecipientType,groups|array',
            'announceSelectedContactIds'   => 'required_if:announceRecipientType,contacts|array',
        ]);

        try {
            $reservation = Reservation::findOrFail($this->announceReservationId);

            $announcement = app(SendEventAnnouncementAction::class)->execute($reservation, [
                'message'        => $this->announceMessage,
                'recipient_type' => $this->announceRecipientType,
                'group_ids'      => $this->announceRecipientType === 'groups'   ? $this->announceSelectedGroupIds   : [],
                'contact_ids'    => $this->announceRecipientType === 'contacts' ? $this->announceSelectedContactIds : [],
            ], auth()->user());

            $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: 'Evento divulgado com sucesso! ({$announcement->total_recipients} destinatarios)' } }))");
            $this->closeAnnounceModal();
        } catch (\Exception $e) {
            $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: '" . $this->safeMessage($e->getMessage()) . "' } }))");
        }
    }

    // ─── Render ───────────────────────────────────────

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
            'reservations' => $query->paginate(10),
        ]);
    }
}
