<?php

namespace App\Livewire\Contact;

use App\Actions\UpdateReservationAction;
use App\Enums\ReservationStatus;
use App\Models\Reservation;
use App\Models\Room;
use App\Services\ConflictCheckerService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app', ['title' => 'Editar Reserva — ESDEP-MT'])]
class ContactReservationEdit extends Component
{
    public $reservationId;
    public string $eventName = '';
    public string $description = '';
    public string $roomId = '';
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
    public string $conflictError = '';

    public function mount(int $reservation): void
    {
        $contact = auth('contact')->user();

        $res = Reservation::findOrFail($reservation);

        if (strtolower($res->requester_email) !== strtolower($contact->email)) {
            abort(403, 'Voce nao tem permissao para editar esta reserva.');
        }

        $this->reservationId = $res->id;
        $this->eventName = $res->event_name;
        $this->description = $res->description ?? '';
        $this->roomId = (string) $res->room_id;
        $this->date = $res->date->format('Y-m-d');
        $this->startTime = $res->start_time;
        $this->endTime = $res->end_time;
        $this->requesterName = $res->requester_name;
        $this->requesterEmail = $res->requester_email;
        $this->attendeesCount = $res->attendees_count;
        $this->coffeeBreak = $res->coffee_break;
        $this->sonorizacao = $res->sonorizacao;
        $this->transmissao = $res->transmissao;
        $this->projetor = $res->projetor;
    }

    public function updatedDate(): void
    {
        $this->checkConflict();
    }

    public function updatedStartTime(): void
    {
        $this->checkConflict();
    }

    public function updatedEndTime(): void
    {
        $this->checkConflict();
    }

    public function updatedRoomId(): void
    {
        $this->checkConflict();
    }

    private function checkConflict(): void
    {
        if (!$this->date || !$this->startTime || !$this->endTime || !$this->roomId) {
            $this->conflictError = '';
            return;
        }

        $conflict = app(ConflictCheckerService::class)->check(
            roomId: $this->roomId,
            date: $this->date,
            startTime: $this->startTime,
            endTime: $this->endTime,
            excludeId: $this->reservationId,
        );

        $this->conflictError = $conflict ? "Conflito com reserva existente na sala {$conflict->room->name} ({$conflict->start_time} - {$conflict->end_time})." : '';
    }

    public function submit(): void
    {
        $this->validate([
            'eventName'      => 'required|string|max:255',
            'requesterName'  => 'required|string|max:255',
            'requesterEmail' => 'required|email|max:255',
            'date'           => 'required|date|after_or_equal:today',
            'startTime'      => 'required',
            'endTime'        => 'required|after:startTime',
            'attendeesCount' => 'required|integer|min:1',
            'roomId'         => 'required|exists:rooms,id',
        ]);

        if ($this->conflictError) {
            session()->flash('error', 'Existem conflitos de horario. Corrija antes de salvar.');
            return;
        }

        $reservation = Reservation::findOrFail($this->reservationId);

        app(UpdateReservationAction::class)->execute($reservation, [
            'event_name'      => $this->eventName,
            'description'     => $this->description,
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
            'projetor'        => $this->projetor,
        ]);

        $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: 'Reserva atualizada com sucesso!' } }))");

        $this->redirect(route('contact.dashboard'));
    }

    public function getRoomsProperty()
    {
        return Room::where('is_active', true)->get();
    }

    public function render()
    {
        return view('livewire.contact.contact-reservation-edit');
    }
}
