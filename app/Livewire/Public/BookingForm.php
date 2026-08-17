<?php

namespace App\Livewire\Public;

use App\Actions\CreateReservationAction;
use App\Models\Room;
use Livewire\Component;

class BookingForm extends Component
{
    public string $roomId = '';
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
    public bool $showSuccess = false;
    public bool $showError = false;
    public string $errorMessage = '';
    public bool $isLoading = false;

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

    public function submit(): void
    {
        $this->validate();

        $this->isLoading = true;
        $this->showError = false;

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
            ]);

            $this->showSuccess = true;
            $this->dispatch('reservationCreated');
            $this->reset([
                'roomId', 'eventName', 'description', 'requesterName',
                'requesterEmail', 'date', 'coffeeBreak', 'sonorizacao', 'transmissao',
            ]);
            $this->startTime = '09:00';
            $this->endTime = '10:00';
            $this->attendeesCount = 1;
        } catch (\Exception $e) {
            $this->showError = true;
            $this->errorMessage = $e->getMessage();
        } finally {
            $this->isLoading = false;
        }
    }

    public function render()
    {
        $room = Room::find($this->roomId);

        return view('livewire.public.booking-form', [
            'room' => $room,
        ]);
    }
}
