<?php

namespace App\Livewire\Contact;

use App\Actions\UpdateReservationAction;
use App\Exceptions\ConflictException;
use App\Models\Reservation;
use App\Models\Room;
use App\Services\ConflictCheckerService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.contact', ['title' => 'Editar Reserva — ESDEP-MT'])]
class ContactReservationEdit extends Component
{
    public int $reservationId = 0;

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
        $res = $this->accessibleReservation($reservation);

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
        if (! $this->date || ! $this->startTime || ! $this->endTime || ! $this->roomId) {
            $this->conflictError = '';

            return;
        }

        try {
            app(ConflictCheckerService::class)->check(
                roomId: (int) $this->roomId,
                date: $this->date,
                startTime: $this->startTime,
                endTime: $this->endTime,
                excludeId: $this->reservationId,
            );
            $this->conflictError = '';
        } catch (ConflictException $exception) {
            $this->conflictError = $exception->getMessage();
        }
    }

    public function submit(): void
    {
        $reservation = $this->accessibleReservation($this->reservationId);
        $allowedRoomIds = $this->allowedRoomsFor($reservation)->pluck('id');

        $this->validate([
            'eventName' => 'required|string|max:255',
            'requesterName' => 'required|string|max:255',
            'requesterEmail' => 'required|email|max:255',
            'date' => 'required|date|after_or_equal:today',
            'startTime' => 'required',
            'endTime' => 'required|after:startTime',
            'attendeesCount' => 'required|integer|min:1',
            'roomId' => ['required', Rule::exists('rooms', 'id')->whereIn('id', $allowedRoomIds)],
        ]);

        if ($this->conflictError) {
            session()->flash('error', 'Existem conflitos de horario. Corrija antes de salvar.');

            return;
        }

        // O e-mail define a propriedade da reserva e nao pode ser transferido pelo formulario.
        $this->requesterEmail = $reservation->requester_email;

        app(UpdateReservationAction::class)->execute($reservation, [
            'event_name' => $this->eventName,
            'description' => $this->description,
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
        ]);

        $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: 'Reserva atualizada com sucesso!' } }))");

        $this->redirect(route('contact.dashboard'));
    }

    public function getRoomsProperty(): Collection
    {
        return $this->allowedRoomsFor($this->accessibleReservation($this->reservationId));
    }

    private function accessibleReservation(int $id): Reservation
    {
        $contact = auth('contact')->user();
        abort_unless($contact !== null, 403);

        return Reservation::query()
            ->whereKey($id)
            ->where(function ($query) use ($contact) {
                $query->whereRaw('LOWER(requester_email) = ?', [strtolower($contact->email)])
                    ->orWhereIn('room_id', $contact->managedRoomIds());
            })
            ->firstOrFail();
    }

    private function allowedRoomsFor(Reservation $reservation): Collection
    {
        $contact = auth('contact')->user();
        if (strtolower($reservation->requester_email) === strtolower($contact->email)) {
            return Room::where('is_active', true)->orderBy('name')->get();
        }

        return Room::where('is_active', true)->whereIn('id', $contact->managedRoomIds())->orderBy('name')->get();
    }

    public function render()
    {
        return view('livewire.contact.contact-reservation-edit');
    }
}
