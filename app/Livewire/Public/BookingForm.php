<?php

namespace App\Livewire\Public;

use App\Actions\CreateReservationAction;
use App\Models\Room;
use Livewire\Component;

class BookingForm extends Component
{
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

    protected $rules = [
        'eventName' => 'required|string|max:255',
        'requesterName' => 'required|string|max:255',
        'requesterEmail' => 'required|email|max:255',
        'date' => 'required|date|after_or_equal:today',
        'startTime' => 'required',
        'endTime' => 'required|after:startTime',
        'attendeesCount' => 'required|integer|min:1',
        'roomId' => 'required|exists:rooms,id',
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

    private function checkConflict(): void
    {
        $this->conflictError = '';

        if (empty($this->date) || empty($this->startTime) || empty($this->endTime) || empty($this->roomId)) {
            return;
        }

        try {
            $conflictChecker = new \App\Services\ConflictCheckerService();
            $conflictChecker->check((int) $this->roomId, $this->date, $this->startTime, $this->endTime);
        } catch (\Exception $e) {
            $this->conflictError = $e->getMessage();
        }
    }

    public function submit(): void
    {
        $this->validate();

        $this->isLoading = true;

        try {
            $action = new CreateReservationAction();
            $action->execute([
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
            ]);

            $this->dispatch('toast', type: 'success', title: 'Reserva enviada com sucesso!', message: 'Aguarde aprovacao do administrador.');
            $this->dispatch('reservationCreated');
            $this->reset([
                'roomId', 'eventName', 'description', 'requesterName',
                'requesterEmail', 'date', 'coffeeBreak', 'sonorizacao', 'transmissao', 'projetor',
            ]);
            $this->startTime = '09:00';
            $this->endTime = '10:00';
            $this->attendeesCount = 1;
        } catch (\Exception $e) {
            $this->dispatch('toast', type: 'error', title: 'Erro ao enviar reserva', message: $e->getMessage());
        } finally {
            $this->isLoading = false;
        }
    }

    public function render()
    {
        return view('livewire.public.booking-form', [
            'rooms' => Room::where('is_active', true)->orderBy('name')->get(),
        ]);
    }
}
