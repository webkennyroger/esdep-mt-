<?php

namespace App\Livewire\Public;

use App\Actions\CreateReservationAction;
use App\Exceptions\ConflictException;
use App\Models\Room;
use App\Services\ConflictCheckerService;
use Illuminate\Validation\Rule;
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

    public string $capacityError = '';

    public $attachment = null;

    public string $attachmentName = '';

    public bool $termsAccepted = false;

    public bool $showTermsModal = false;

    public bool $standalone = false;

    public function mount(): void
    {
        $this->standalone = request()->routeIs('reservations.create', 'contact.booking');
        if ($contact = auth('contact')->user()) {
            $this->requesterName = $contact->name;
            $this->requesterEmail = strtolower($contact->email);
        }
    }

    protected $rules = [
        'eventName' => 'required|string|max:255',
        'requesterName' => 'required|string|max:255',
        'requesterEmail' => 'required|email|max:255',
        'date' => 'required|date|after_or_equal:today',
        'startTime' => 'required',
        'endTime' => 'required|after:startTime',
        'attendeesCount' => 'required|integer|min:1',
        'roomId' => 'required',
        'attachment' => 'nullable|file|max:5120|mimes:pdf,png,jpg,jpeg',
        'termsAccepted' => 'accepted',
    ];

    protected $messages = [
        'eventName.required' => 'O nome do evento é obrigatório.',
        'requesterName.required' => 'O nome do solicitante é obrigatório.',
        'requesterEmail.required' => 'O e-mail é obrigatório.',
        'requesterEmail.email' => 'Insira um e-mail válido.',
        'date.required' => 'A data é obrigatória.',
        'date.after_or_equal' => 'A data deve ser hoje ou futura.',
        'startTime.required' => 'O horário de início é obrigatório.',
        'endTime.required' => 'O horário de fim é obrigatório.',
        'endTime.after' => 'O horário de fim deve ser após o início.',
        'attendeesCount.required' => 'O número de participantes é obrigatório.',
        'attendeesCount.min' => 'Deve haver pelo menos 1 participante.',
        'roomId.required' => 'Selecione uma sala.',
        'roomId.exists' => 'A sala selecionada não existe.',
        'attachment.max' => 'O arquivo deve ter no máximo 5MB.',
        'attachment.mimes' => 'O arquivo deve ser PDF, PNG ou JPG.',
        'termsAccepted.accepted' => 'Você precisa aceitar os termos de compromisso para continuar.',
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
        $this->checkCapacity();
    }

    public function updatedAttendeesCount(): void
    {
        $this->checkCapacity();
    }

    private function checkCapacity(): void
    {
        $this->capacityError = '';

        if (empty($this->roomId) || $this->attendeesCount < 1) {
            return;
        }

        $room = Room::find((int) $this->roomId);
        if ($room && $this->attendeesCount > $room->capacity) {
            $this->capacityError = "O numero de participantes ({$this->attendeesCount}) excede a capacidade da sala \"{$room->name}\" ({$room->capacity} pessoas).";
        }
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
        $this->validate(array_merge($this->rules, [
            'roomId' => ['required', Rule::exists('rooms', 'id')->where('is_active', true)],
        ]));

        $this->checkConflict();
        if ($this->conflictError) {
            $this->toast('error', 'Conflito de horário', $this->conflictError);
            $this->isLoading = false;

            return;
        }

        $this->checkCapacity();
        if ($this->capacityError) {
            $this->toast('error', 'Capacidade excedida', $this->capacityError);
            $this->isLoading = false;

            return;
        }

        $this->isLoading = true;

        try {
            $data = [
                'room_id' => $this->roomId,
                'event_name' => $this->eventName,
                'description' => $this->description,
                'requester_name' => $this->requesterName,
                'requester_email' => $this->requesterEmail,
                'date' => $this->date,
                'start_time' => $this->startTime,
                'end_time' => $this->endTime,
                'attendees_count' => $this->attendeesCount,
                'coffee_break' => $this->coffeeBreak,
                'sonorizacao' => $this->sonorizacao,
                'transmissao' => $this->transmissao,
                'projetor' => $this->projetor,
                'terms_accepted' => $this->termsAccepted,
                'terms_accepted_at' => $this->termsAccepted ? now() : null,
            ];

            if ($this->attachment) {
                $path = $this->attachment->store('attachments', 'public');
                $data['attachment_path'] = $path;
                $data['attachment_name'] = $this->attachmentName;
            }

            app(CreateReservationAction::class)->execute($data);

            $this->toast('success', 'Reserva enviada com sucesso!', 'Aguarde aprovação do administrador.');
            $this->dispatch('reservationCreated');
            $this->reset([
                'roomId', 'eventName', 'description', 'requesterName',
                'requesterEmail', 'date', 'coffeeBreak', 'sonorizacao', 'transmissao', 'projetor',
                'attachment', 'attachmentName', 'termsAccepted',
            ]);
            $this->startTime = '09:00';
            $this->endTime = '10:00';
            $this->attendeesCount = 1;
        } catch (ConflictException $e) {
            $this->conflictError = $e->getMessage();
            $this->toast('error', 'Conflito de horário', $this->conflictError);
        } catch (\Exception $e) {
            report($e);
            $this->toast('error', 'Erro ao enviar reserva', 'Não foi possível registrar a reserva. Tente novamente.');
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
        $this->resetErrorBag('termsAccepted');
    }

    public function updatedTermsAccepted(): void
    {
        if (! $this->termsAccepted) {
            $this->validate(['termsAccepted' => 'accepted']);
        } else {
            $this->resetErrorBag('termsAccepted');
        }
    }

    public function render()
    {
        return view('livewire.public.booking-form', [
            'rooms' => Room::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    private function toast(string $type, string $title, string $message): void
    {
        $detail = json_encode(compact('type', 'title', 'message'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
        $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: {$detail} }))");
    }
}
