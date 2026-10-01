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
use App\Rules\MultipleEmails;
use App\Services\ConflictCheckerService;
use App\Services\ReservationExportService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class ReservationManager extends Component
{
    use AuthorizesRequests, WithPagination;

    public string $activeFilter = 'all';

    public string $search = '';

    public array $selected = [];

    public bool $selectAll = false;

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

    public string $announceRecipientType = '';

    public array $announceSelectedGroupIds = [];

    public array $announceSelectedContactIds = [];

    public string $announceExtraEmails = '';

    public $announceGroups = [];

    public $announceContacts = [];

    public string $detailConflictError = '';

    protected $listeners = [
        'reservationCreated' => '$refresh',
        'reservationUpdated' => '$refresh',
        'reservationApproved' => '$refresh',
        'reservationRejected' => '$refresh',
    ];

    // ─── Helpers ─────────────────────────────────────

    /**
     * Escapa uma mensagem de exceção para uso seguro em JS inline via CustomEvent.
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
        $this->selected = [];
        $this->selectAll = false;
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->selected = [];
        $this->selectAll = false;
    }

    public function updatedSelectAll(bool $value): void
    {
        $this->selected = $value ? $this->baseQuery()->paginate(10)->pluck('id')->all() : [];
    }

    private function baseQuery()
    {
        $query = Reservation::accessibleTo(auth()->user())->with('room')->orderBy('sort_order')->latest('created_at');

        if ($this->activeFilter !== 'all') {
            $query->where('status', $this->activeFilter);
        }

        if (! empty($this->search)) {
            $term = '%'.strtolower($this->search).'%';
            $query->where(function ($q) use ($term) {
                $q->whereRaw('LOWER(event_name) LIKE ?', [$term])
                    ->orWhereRaw('LOWER(requester_name) LIKE ?', [$term]);
            });
        }

        return $query;
    }

    // ─── Detail Modal ─────────────────────────────────

    public function openDetailModal(int $id): void
    {
        $this->detailReservation = Reservation::accessibleTo(auth()->user())->with(['room'])->findOrFail($id);
        $this->authorize('view', $this->detailReservation);
        $this->detailConflictError = '';

        if ($this->detailReservation->status === ReservationStatus::PENDING) {
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
        $this->authorize('update', Reservation::accessibleTo(auth()->user())->findOrFail($id));
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
            $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: 'Reserva não identificada. Atualize a página e tente novamente.' } }))");
            $this->closeApproveModal();

            return;
        }

        try {
            $reservation = Reservation::accessibleTo(auth()->user())->findOrFail($reservationId);
            $this->authorize('update', $reservation);
            app(ApproveReservationAction::class)->execute($reservation);
            $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: 'Reserva autorizada com sucesso.' } }))");
            $this->dispatch('reservationApproved');
        } catch (\Exception $e) {
            $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: '".$this->safeMessage($e->getMessage())."' } }))");
        }

        $this->closeApproveModal();
    }

    // ─── Reject Modal ─────────────────────────────────

    public function openRejectModal(int $id, string $name): void
    {
        $this->authorize('update', Reservation::accessibleTo(auth()->user())->findOrFail($id));
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
            $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: 'Reserva não identificada. Atualize a página e tente novamente.' } }))");
            $this->closeRejectModal();

            return;
        }

        try {
            $reservation = Reservation::accessibleTo(auth()->user())->findOrFail($reservationId);
            $this->authorize('update', $reservation);
            app(RejectReservationAction::class)->execute($reservation, $this->rejectReason ?: null);
            $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: 'Reserva rejeitada com sucesso.' } }))");
            $this->dispatch('reservationRejected');
        } catch (\Exception $e) {
            $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: '".$this->safeMessage($e->getMessage())."' } }))");
        }

        $this->closeRejectModal();
    }

    // ─── Reopen ───────────────────────────────────────

    public function reopenReservation(int $id): void
    {
        try {
            DB::transaction(function () use ($id) {
                $reservation = Reservation::accessibleTo(auth()->user())->findOrFail($id);
                $this->authorize('update', $reservation);

                // Verifica conflito antes de reabrir
                app(ConflictCheckerService::class)->check(
                    $reservation->room_id,
                    $reservation->date,
                    $reservation->start_time,
                    $reservation->end_time,
                    $reservation->id
                );

                $reservation->status = ReservationStatus::PENDING;
                $reservation->reject_reason = null;
                $reservation->save();
            });

            $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: 'Reserva reaberta como pendente.' } }))");
            $this->dispatch('reservationRejected');
        } catch (\Exception $e) {
            $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: '".$this->safeMessage($e->getMessage())."' } }))");
        }
    }

    public function reorderReservations(array $ids): void
    {
        $query = Reservation::accessibleTo(auth()->user());
        abort_if((clone $query)->whereIn('id', $ids)->count() !== count(array_unique($ids)), 403);
        foreach ($ids as $position => $id) {
            (clone $query)->whereKey($id)->update(['sort_order' => $position]);
        }
    }

    // ─── Announce Modal ───────────────────────────────

    public function openAnnounceModal(int $id, string $name): void
    {
        $this->authorize('view', Reservation::accessibleTo(auth()->user())->findOrFail($id));
        $this->announceReservationId = $id;
        $this->announceReservationName = $name;
        $this->announceMessage = '';
        $this->announceRecipientType = '';
        $this->announceSelectedGroupIds = [];
        $this->announceSelectedContactIds = [];
        $this->announceExtraEmails = '';
        $this->announceGroups = ContactGroup::orderBy('name')->get();
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
            'announceRecipientType' => 'nullable|in:groups,contacts',
            'announceSelectedGroupIds' => 'required_if:announceRecipientType,groups|array',
            'announceSelectedContactIds' => 'required_if:announceRecipientType,contacts|array',
            'announceExtraEmails' => ['nullable', 'string', 'max:1000', new MultipleEmails],
        ]);

        try {
            $reservation = Reservation::accessibleTo(auth()->user())->findOrFail($this->announceReservationId);
            $this->authorize('view', $reservation);

            $announcement = app(SendEventAnnouncementAction::class)->execute($reservation, [
                'message' => $this->announceMessage,
                'recipient_type' => $this->announceRecipientType,
                'group_ids' => $this->announceRecipientType === 'groups' ? $this->announceSelectedGroupIds : [],
                'contact_ids' => $this->announceRecipientType === 'contacts' ? $this->announceSelectedContactIds : [],
                'extra_emails' => $this->announceExtraEmails,
            ], auth()->user());

            $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: 'Evento divulgado com sucesso! ({$announcement->total_recipients} destinatários)' } }))");
            $this->closeAnnounceModal();
        } catch (\Exception $e) {
            $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: '".$this->safeMessage($e->getMessage())."' } }))");
        }
    }

    // ─── Export ──────────────────────────────────────

    /**
     * Reservas selecionadas via checkbox, ou todas as que combinam com o
     * filtro/busca atual quando nada estiver selecionado.
     */
    private function reservationsForExport(): \Illuminate\Support\Collection
    {
        if (! empty($this->selected)) {
            return Reservation::accessibleTo(auth()->user())->with('room')->whereIn('id', $this->selected)->get();
        }

        return $this->baseQuery()->get();
    }

    public function exportExcel(): Response
    {
        $bytes = app(ReservationExportService::class)->toXlsx($this->reservationsForExport());

        return response($bytes, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="reservas-'.now()->format('Y-m-d').'.xlsx"',
        ]);
    }

    public function exportPdf(): Response
    {
        $bytes = app(ReservationExportService::class)->toPdf($this->reservationsForExport());

        return response($bytes, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="reservas-'.now()->format('Y-m-d').'.pdf"',
        ]);
    }

    public function copyText(): string
    {
        $service = app(ReservationExportService::class);
        $reservations = $this->reservationsForExport();

        $lines = [implode("\t", ReservationExportService::HEADERS)];
        foreach ($service->rows($reservations) as $row) {
            $lines[] = implode("\t", $row);
        }

        return implode("\n", $lines);
    }

    public function printHtml(): string
    {
        $service = app(ReservationExportService::class);
        $reservations = $this->reservationsForExport();

        return view('exports.print-table', [
            'headers' => ReservationExportService::HEADERS,
            'rows' => $service->rows($reservations),
        ])->render();
    }

    // ─── Render ───────────────────────────────────────

    public function render()
    {
        $countsQuery = Reservation::accessibleTo(auth()->user());

        return view('livewire.admin.reservation-manager', [
            'reservations' => $this->baseQuery()->paginate(10),
            'filterCounts' => [
                'all' => (clone $countsQuery)->count(),
                'pending' => (clone $countsQuery)->where('status', ReservationStatus::PENDING)->count(),
                'authorized' => (clone $countsQuery)->where('status', ReservationStatus::AUTHORIZED)->count(),
                'rejected' => (clone $countsQuery)->where('status', ReservationStatus::REJECTED)->count(),
            ],
        ]);
    }
}
