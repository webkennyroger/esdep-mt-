<?php

namespace App\Livewire\Admin;

use App\Actions\CreateReservationAction;
use App\Actions\UpdateReservationAction;
use App\Enums\ReservationStatus;
use App\Exceptions\ConflictException;
use App\Models\Reservation;
use App\Models\Room;
use App\Services\ConflictCheckerService;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
class ReservationEditor extends Component
{
    use AuthorizesRequests, WithFileUploads;

    public bool $isPageMode = false;

    public bool $showModal = false;

    public int $reservationId = 0;

    public string $eventName = '';

    public string $description = '';

    public int $roomId = 0;

    public string $date = '';

    public string $startTime = '';

    public string $endTime = '';

    public string $requesterName = '';

    public string $requesterEmail = '';

    public int $attendeesCount = 1;

    public bool $coffeeBreak = false;

    public bool $sonorizacao = false;

    public bool $transmissao = false;

    public bool $projetor = false;

    public $attachment = null;

    public ?string $existingAttachmentPath = null;

    public string $existingAttachmentName = '';

    public bool $removeExistingAttachment = false;

    public string $conflictError = '';

    protected function rules(): array
    {
        return [
            'eventName' => 'required|string|max:255',
            'description' => 'nullable|string',
            'roomId' => ['required', Rule::exists('rooms', 'id')->whereIn('id', Room::accessibleTo(auth()->user())->pluck('id'))],
            'date' => 'required|date|after_or_equal:today',
            'startTime' => 'required',
            'endTime' => 'required|after:startTime',
            'requesterName' => 'required|string|max:255',
            'requesterEmail' => 'required|email|max:255',
            'attendeesCount' => 'required|integer|min:1',
            'coffeeBreak' => 'boolean',
            'sonorizacao' => 'boolean',
            'transmissao' => 'boolean',
            'projetor' => 'boolean',
            'attachment' => 'nullable|file|max:5120|mimes:pdf,png,jpg,jpeg',
        ];
    }

    protected $messages = [
        'eventName.required' => 'O nome do evento é obrigatório.',
        'roomId.required' => 'Selecione uma sala.',
        'roomId.exists' => 'A sala selecionada não existe.',
        'date.required' => 'A data é obrigatória.',
        'date.after_or_equal' => 'A data deve ser hoje ou futura.',
        'startTime.required' => 'O horário de início é obrigatório.',
        'endTime.required' => 'O horário de fim é obrigatório.',
        'endTime.after' => 'O horário de fim deve ser posterior ao horário de início.',
        'requesterName.required' => 'O nome do solicitante é obrigatório.',
        'requesterEmail.required' => 'O e-mail é obrigatório.',
        'requesterEmail.email' => 'Insira um e-mail válido.',
        'attendeesCount.required' => 'O número de participantes é obrigatório.',
        'attendeesCount.min' => 'Deve haver pelo menos 1 participante.',
        'attachment.max' => 'O arquivo deve ter no máximo 5MB.',
        'attachment.mimes' => 'O arquivo deve ser PDF, PNG ou JPG.',
    ];

    protected $listeners = [
        'openReservationEditor' => 'open',
        'openReservationCreator' => 'openCreate',
    ];

    public function mount(): void
    {
        if (request()->routeIs('admin.reservations.create')) {
            $this->isPageMode = true;
            $this->openCreate();
        }
    }

    public function updatedDate(): void
    {
        $this->validate(['date' => $this->rules()['date']]);
        $this->checkConflict();
    }

    public function updatedStartTime(): void
    {
        $this->validate(['startTime' => $this->rules()['startTime']]);
        $this->checkConflict();
    }

    public function updatedEndTime(): void
    {
        $this->validate(['endTime' => $this->rules()['endTime']]);
        $this->checkConflict();
    }

    public function updatedRoomId(): void
    {
        $this->checkConflict();
    }

    /**
     * @return array{attachment_path?: string|null, attachment_name?: string|null}
     */
    private function resolveAttachmentData(): array
    {
        if ($this->attachment) {
            if ($this->existingAttachmentPath) {
                Storage::disk('public')->delete($this->existingAttachmentPath);
            }

            return [
                'attachment_path' => $this->attachment->store('attachments', 'public'),
                'attachment_name' => $this->attachment->getClientOriginalName(),
            ];
        }

        if ($this->removeExistingAttachment && $this->existingAttachmentPath) {
            Storage::disk('public')->delete($this->existingAttachmentPath);

            return ['attachment_path' => null, 'attachment_name' => null];
        }

        return [];
    }

    private function checkConflict(): void
    {
        $this->conflictError = '';

        if (empty($this->date) || empty($this->startTime) || empty($this->endTime) || empty($this->roomId)) {
            return;
        }

        try {
            app(ConflictCheckerService::class)->check($this->roomId, $this->date, $this->startTime, $this->endTime, $this->reservationId);
        } catch (ConflictException $e) {
            $this->conflictError = $e->getMessage();
        }
    }

    public function open(int $id): void
    {
        $reservation = Reservation::accessibleTo(auth()->user())->with('room')->findOrFail($id);
        $this->authorize('update', $reservation);

        $this->reservationId = $reservation->id;
        $this->eventName = $reservation->event_name;
        $this->description = $reservation->description ?? '';
        $this->roomId = $reservation->room_id;
        $this->date = Carbon::parse($reservation->date)->format('Y-m-d');
        $this->startTime = Carbon::parse($reservation->start_time)->format('H:i');
        $this->endTime = Carbon::parse($reservation->end_time)->format('H:i');
        $this->requesterName = $reservation->requester_name;
        $this->requesterEmail = $reservation->requester_email;
        $this->attendeesCount = $reservation->attendees_count;
        $this->coffeeBreak = (bool) $reservation->coffee_break;
        $this->sonorizacao = (bool) $reservation->sonorizacao;
        $this->transmissao = (bool) $reservation->transmissao;
        $this->projetor = (bool) $reservation->projetor;
        $this->attachment = null;
        $this->existingAttachmentPath = $reservation->attachment_path;
        $this->existingAttachmentName = $reservation->attachment_name ?? '';
        $this->removeExistingAttachment = false;

        $this->conflictError = '';
        $this->resetValidation();
        $this->showModal = true;
    }

    public function openCreate(): void
    {
        $this->authorize('create', Reservation::class);

        $this->reservationId = 0;
        $this->eventName = '';
        $this->description = '';
        $this->roomId = 0;
        $this->date = '';
        $this->startTime = '';
        $this->endTime = '';
        $this->requesterName = '';
        $this->requesterEmail = '';
        $this->attendeesCount = 1;
        $this->coffeeBreak = false;
        $this->sonorizacao = false;
        $this->transmissao = false;
        $this->projetor = false;
        $this->attachment = null;
        $this->existingAttachmentPath = null;
        $this->existingAttachmentName = '';
        $this->removeExistingAttachment = false;

        $this->conflictError = '';
        $this->resetValidation();
        $this->showModal = true;
    }

    public function markAttachmentForRemoval(): void
    {
        $this->attachment = null;
        $this->removeExistingAttachment = true;
    }

    public function close(): void
    {
        $this->showModal = false;
        $this->resetValidation();
    }

    public function save(): void
    {
        $this->validate();

        if ($this->reservationId === 0) {
            $this->createReservation();

            return;
        }

        $reservation = Reservation::accessibleTo(auth()->user())->findOrFail($this->reservationId);
        $this->authorize('update', $reservation);

        try {
            app(UpdateReservationAction::class)->execute($reservation, [
                'event_name' => $this->eventName,
                'description' => $this->description ?: null,
                'room_id' => $this->roomId,
                'date' => $this->date,
                'start_time' => $this->startTime,
                'end_time' => $this->endTime,
                'requester_name' => $this->requesterName,
                'requester_email' => $this->requesterEmail,
                'attendees_count' => $this->attendeesCount,
                'coffee_break' => $this->coffeeBreak,
                'sonorizacao' => $this->sonorizacao,
                'transmissao' => $this->transmissao,
                'projetor' => $this->projetor,
                ...$this->resolveAttachmentData(),
            ]);

            $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: 'Reserva atualizada com sucesso.' } }))");
            $this->dispatch('reservationUpdated');
            $this->close();
        } catch (\Exception $e) {
            $safeMsg = addslashes(strip_tags($e->getMessage()));
            $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: '{$safeMsg}' } }))");
        }
    }

    private function createReservation(): void
    {
        $this->authorize('create', Reservation::class);

        try {
            app(CreateReservationAction::class)->execute([
                'event_name' => $this->eventName,
                'description' => $this->description ?: null,
                'room_id' => $this->roomId,
                'date' => $this->date,
                'start_time' => $this->startTime,
                'end_time' => $this->endTime,
                'requester_name' => $this->requesterName,
                'requester_email' => $this->requesterEmail,
                'attendees_count' => $this->attendeesCount,
                'coffee_break' => $this->coffeeBreak,
                'sonorizacao' => $this->sonorizacao,
                'transmissao' => $this->transmissao,
                'projetor' => $this->projetor,
                'status' => ReservationStatus::AUTHORIZED->value,
                ...$this->resolveAttachmentData(),
            ], notify: false);

            $this->dispatch('reservationCreated');

            if ($this->isPageMode) {
                $this->redirect(route('admin.reservations.index'));

                return;
            }

            $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: 'Reserva criada com sucesso.' } }))");
            $this->close();
        } catch (\Exception $e) {
            $safeMsg = addslashes(strip_tags($e->getMessage()));
            $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: '{$safeMsg}' } }))");
        }
    }

    public function render()
    {
        return view('livewire.admin.reservation-editor', [
            'rooms' => Room::accessibleTo(auth()->user())->where('is_active', true)->orderBy('name')->get(),
        ]);
    }
}
