<?php

namespace App\Livewire\Public;

use App\Actions\CreateReservationAction;
use App\Exceptions\ConflictException;
use App\Models\Room;
use App\Services\ConflictCheckerService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app', ['title' => 'Nova Reserva — ESDEP-MT'])]
class BookingForm extends Component
{
    use WithFileUploads;

    public string $roomId = '1';
    public string $eventName = '';
    public string $description = '';
    public string $requesterName = '';
    public string $requesterEmail = '';
    public string $date = '';
    public string $startTime = '09:00';
    public string $endTime = '10:00';
    public int $attendeesCount = 1;
    public bool $coffeeBreak = false;
    public bool $sonorizacao = false;
    public bool $transmissao = false;
    public bool $projetor = false;
    public bool $isLoading = false;
    public string $conflictError = '';
    public $attachment = null;
    public string $attachmentName = '';
    public bool $termsAccepted = false;
    public bool $showTermsModal = false;
    public bool $standalone = false;

    public function mount(): void
    {
        $this->standalone = request()->routeIs('reservations.create');
    }

    protected $rules = [
        'eventName'      => 'required|string|max:255',
        'requesterName'  => 'required|string|max:255',
        'requesterEmail' => 'required|email|max:255',
        'date'           => 'required|date|after_or_equal:today',
        'startTime'      => 'required',
        'endTime'        => 'required|after:startTime',
        'attendeesCount' => 'required|integer|min:1',
        'roomId'         => 'required|exists:rooms,id',
        'attachment'     => 'nullable|file|max:5120|mimes:pdf,png,jpg,jpeg',
        'termsAccepted'  => 'accepted',
    ];

    protected $messages = [
        'eventName.required'      => 'O nome do evento é obrigatório.',
        'requesterName.required'  => 'O nome do solicitante é obrigatório.',
        'requesterEmail.required' => 'O e-mail é obrigatório.',
        'requesterEmail.email'    => 'Insira um e-mail válido.',
        'date.required'           => 'A data é obrigatória.',
        'date.after_or_equal'     => 'A data deve ser hoje ou futura.',
        'startTime.required'      => 'O horário de início é obrigatório.',
        'endTime.required'        => 'O horário de fim é obrigatório.',
        'endTime.after'           => 'O horário de fim deve ser após o início.',
        'attendeesCount.required' => 'O número de participantes é obrigatório.',
        'attendeesCount.min'      => 'Deve haver pelo menos 1 participante.',
        'roomId.required'         => 'Selecione uma sala.',
        'roomId.exists'           => 'A sala selecionada não existe.',
        'attachment.max'          => 'O arquivo deve ter no máximo 5MB.',
        'attachment.mimes'        => 'O arquivo deve ser PDF, PNG ou JPG.',
        'termsAccepted.accepted'  => 'Você precisa aceitar os termos de compromisso para continuar.',
    ];

    public function updatedDate(): void
    {
        $this->validate(['date' => $this->rules['date']]);
        $this->checkConflict();
    }

    public function updatedStartTime(): void
    {
        $this->validate(['startTime' => $this->rules['startTime']]);
        $this->checkConflict();
    }

    public function updatedEndTime(): void
    {
        $this->validate(['endTime' => $this->rules['endTime']]);
        $this->checkConflict();
    }

    public function updatedRoomId(): void
    {
        $this->checkConflict();
    }

    public function updatedAttachment(): void
    {
        if ($this->attachment) {
            $this->attachmentName = $this->attachment->getClientOriginalName();
            $this->validate(['attachment' => $this->rules['attachment']]);
        }
    }

    private function checkConflict(): void
    {
        $this->conflictError = '';

        if (empty($this->date) || empty($this->startTime) || empty($this->endTime) || empty($this->roomId)) {
            return;
        }

        try {
            app(ConflictCheckerService::class)->check((int) $this->roomId, $this->date, $this->startTime, $this->endTime);
        } catch (ConflictException $e) {
            $this->conflictError = $e->getMessage();
        }
    }

    public function submit(): void
    {
        $this->validate();

        $this->isLoading = true;

        try {
            $data = [
                'room_id'         => $this->roomId,
                'event_name'      => $this->eventName,
                'description'     => $this->description,
                'requester_name'  => $this->requesterName,
                'requester_email' => $this->requesterEmail,
                'date'            => $this->date,
                'start_time'      => $this->startTime,
                'end_time'        => $this->endTime,
                'attendees_count' => $this->attendeesCount,
                'coffee_break'    => $this->coffeeBreak,
                'sonorizacao'     => $this->sonorizacao,
                'transmissao'     => $this->transmissao,
                'projetor'        => $this->projetor,
                'terms_accepted'  => $this->termsAccepted,
                'terms_accepted_at' => $this->termsAccepted ? now() : null,
            ];

            if ($this->attachment) {
                $path = $this->attachment->store('attachments', 'public');
                $data['attachment_path'] = $path;
                $data['attachment_name'] = $this->attachmentName;
            }

            app(CreateReservationAction::class)->execute($data);

            $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', title: 'Reserva enviada com sucesso!', message: 'Aguarde aprovação do administrador.' } }))");
            $this->dispatch('reservationCreated');
            $this->reset([
                'roomId', 'eventName', 'description', 'requesterName',
                'requesterEmail', 'date', 'coffeeBreak', 'sonorizacao', 'transmissao', 'projetor',
                'attachment', 'attachmentName', 'termsAccepted',
            ]);
            $this->startTime = '09:00';
            $this->endTime = '10:00';
            $this->attendeesCount = 1;
        } catch (\Exception $e) {
            $safeMsg = addslashes(strip_tags($e->getMessage()));
            $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', title: 'Erro ao enviar reserva', message: '{$safeMsg}' } }))");
        } finally {
            $this->isLoading = false;
        }
    }

    public function openTermsModal(): void
    {
        $this->showTermsModal = true;
    }

    public function closeTermsModal(): void
    {
        $this->showTermsModal = false;
    }

    public function acceptTerms(): void
    {
        $this->termsAccepted = true;
        $this->showTermsModal = false;
    }

    public function render()
    {
        return view('livewire.public.booking-form', [
            'rooms' => Room::where('is_active', true)->orderBy('name')->get(),
        ]);
    }
}
