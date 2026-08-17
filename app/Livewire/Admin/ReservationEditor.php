<?php

namespace App\Livewire\Admin;

use App\Actions\UpdateReservationAction;
use App\Models\Reservation;
use App\Models\Room;
use Livewire\Component;

class ReservationEditor extends Component
{
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

    protected function rules(): array
    {
        return [
            'eventName'      => 'required|string|max:255',
            'description'    => 'nullable|string',
            'roomId'         => 'required|exists:rooms,id',
            'date'           => 'required|date|after_or_equal:today',
            'startTime'      => 'required',
            'endTime'        => 'required|after:startTime',
            'requesterName'  => 'required|string|max:255',
            'requesterEmail' => 'required|email|max:255',
            'attendeesCount' => 'required|integer|min:1',
            'coffeeBreak'    => 'boolean',
            'sonorizacao'    => 'boolean',
            'transmissao'    => 'boolean',
        ];
    }

    protected $messages = [
        'eventName.required'      => 'O nome do evento e obrigatorio.',
        'roomId.required'         => 'Selecione uma sala.',
        'roomId.exists'           => 'A sala selecionada nao existe.',
        'date.required'           => 'A data e obrigatoria.',
        'date.after_or_equal'     => 'A data deve ser hoje ou futura.',
        'startTime.required'      => 'O horario de inicio e obrigatorio.',
        'endTime.required'        => 'O horario de fim e obrigatorio.',
        'endTime.after'           => 'O horario de fim deve ser posterior ao horario de inicio.',
        'requesterName.required'  => 'O nome do solicitante e obrigatorio.',
        'requesterEmail.required' => 'O e-mail e obrigatorio.',
        'requesterEmail.email'    => 'Insira um e-mail valido.',
        'attendeesCount.required' => 'O numero de participantes e obrigatorio.',
        'attendeesCount.min'      => 'Deve haver pelo menos 1 participante.',
    ];

    protected $listeners = [
        'openReservationEditor' => 'open',
    ];

    public function open(int $id): void
    {
        $reservation = Reservation::with('room')->findOrFail($id);

        $this->reservationId  = $reservation->id;
        $this->eventName      = $reservation->event_name;
        $this->description    = $reservation->description ?? '';
        $this->roomId         = $reservation->room_id;
        $this->date           = \Carbon\Carbon::parse($reservation->date)->format('Y-m-d');
        $this->startTime      = \Carbon\Carbon::parse($reservation->start_time)->format('H:i');
        $this->endTime        = \Carbon\Carbon::parse($reservation->end_time)->format('H:i');
        $this->requesterName  = $reservation->requester_name;
        $this->requesterEmail = $reservation->requester_email;
        $this->attendeesCount = $reservation->attendees_count;
        $this->coffeeBreak    = (bool) $reservation->coffee_break;
        $this->sonorizacao    = (bool) $reservation->sonorizacao;
        $this->transmissao    = (bool) $reservation->transmissao;

        $this->resetValidation();
        $this->showModal = true;
    }

    public function close(): void
    {
        $this->showModal = false;
        $this->resetValidation();
    }

    public function save(): void
    {
        $this->validate();

        $reservation = Reservation::findOrFail($this->reservationId);

        try {
            $action = new UpdateReservationAction();
            $action->execute($reservation, [
                'event_name'      => $this->eventName,
                'description'     => $this->description ?: null,
                'room_id'         => $this->roomId,
                'date'            => $this->date,
                'start_time'      => $this->startTime,
                'end_time'        => $this->endTime,
                'requester_name'  => $this->requesterName,
                'requester_email' => $this->requesterEmail,
                'attendees_count' => $this->attendeesCount,
                'coffee_break'    => $this->coffeeBreak,
                'sonorizacao'     => $this->sonorizacao,
                'transmissao'     => $this->transmissao,
            ]);

            session()->flash('success', 'Reserva atualizada com sucesso.');
            $this->dispatch('reservationUpdated');
            $this->close();
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.reservation-editor', [
            'rooms' => Room::where('is_active', true)->orderBy('name')->get(),
        ]);
    }
}
