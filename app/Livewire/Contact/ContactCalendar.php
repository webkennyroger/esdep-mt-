<?php

namespace App\Livewire\Contact;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.contact', ['title' => 'Agenda das Salas — ESDEP-MT'])]
class ContactCalendar extends Component
{
    public int $currentMonth;

    public int $currentYear;

    public array $reservations = [];

    public string $selectedDate = '';

    public array $dayReservations = [];

    protected $listeners = [
        'reservationCreated' => 'loadReservations',
        'reservationUpdated' => 'loadReservations',
        'reservationApproved' => 'loadReservations',
        'reservationRejected' => 'loadReservations',
    ];

    public function mount(): void
    {
        Carbon::setLocale('pt_BR');
        $this->currentMonth = (int) now()->format('m');
        $this->currentYear = (int) now()->format('Y');
        $this->loadReservations();
    }

    public function loadReservations(): void
    {
        $contact = auth('contact')->user();
        $start = Carbon::create($this->currentYear, $this->currentMonth, 1)->startOfMonth()->toDateString();
        $end = Carbon::create($this->currentYear, $this->currentMonth, 1)->endOfMonth()->toDateString();
        $rows = Reservation::with('room')->whereIn('room_id', $contact->managedRoomIds())
            ->whereBetween('date', [$start, $end])->where('status', ReservationStatus::AUTHORIZED)->get();
        $this->reservations = [];
        foreach ($rows as $row) {
            $key = $row->date->format('Y-m-d');
            $this->reservations[$key][] = [
                'id' => $row->id, 'event_name' => $row->event_name, 'description' => $row->description,
                'requester_name' => $row->requester_name, 'requester_email' => $row->requester_email,
                'attendees_count' => $row->attendees_count, 'start_time' => Carbon::parse($row->start_time)->format('H:i'),
                'end_time' => Carbon::parse($row->end_time)->format('H:i'), 'room_name' => $row->room->name,
                'coffee_break' => (bool) $row->coffee_break, 'sonorizacao' => (bool) $row->sonorizacao,
                'transmissao' => (bool) $row->transmissao,
            ];
        }
    }

    public function previousMonth(): void
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth)->subMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
        $this->clearSelection();
        $this->loadReservations();
    }

    public function nextMonth(): void
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth)->addMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
        $this->clearSelection();
        $this->loadReservations();
    }

    public function selectDate(string $date): void
    {
        $this->selectedDate = $date;
        $this->dayReservations = $this->reservations[$date] ?? [];
    }

    public function getDaysOfWeekProperty(): array
    {
        return ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'];
    }

    public function getMonthNameProperty(): string
    {
        return Carbon::create($this->currentYear, $this->currentMonth)->translatedFormat('F \\d\\e Y');
    }

    public function getCalendarDaysProperty(): array
    {
        $start = Carbon::create($this->currentYear, $this->currentMonth, 1);

        return array_merge(array_fill(0, $start->dayOfWeek, null), range(1, $start->daysInMonth));
    }

    private function clearSelection(): void
    {
        $this->selectedDate = '';
        $this->dayReservations = [];
    }

    public function render()
    {
        return view('livewire.admin.admin-calendar');
    }
}
