<?php

namespace App\Livewire\Admin;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class AdminCalendar extends Component
{
    public int $currentMonth;
    public int $currentYear;
    public array $reservations = [];
    public string $selectedDate = '';
    public array $dayReservations = [];

    protected $listeners = [
        'reservationCreated' => '$refresh',
        'reservationApproved' => '$refresh',
        'reservationRejected' => '$refresh',
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
        $start = Carbon::create($this->currentYear, $this->currentMonth, 1)->startOfMonth()->toDateString();
        $end = Carbon::create($this->currentYear, $this->currentMonth, 1)->endOfMonth()->toDateString();

        $rows = Reservation::with('room')
            ->whereDate('date', '>=', $start)
            ->whereDate('date', '<=', $end)
            ->where('status', ReservationStatus::AUTHORIZED)
            ->get();

        $this->reservations = [];
        foreach ($rows as $row) {
            $key = Carbon::parse($row->date)->format('Y-m-d');
            $this->reservations[$key][] = [
                'id' => $row->id,
                'event_name' => $row->event_name,
                'description' => $row->description,
                'requester_name' => $row->requester_name,
                'requester_email' => $row->requester_email,
                'attendees_count' => $row->attendees_count,
                'start_time' => Carbon::parse($row->start_time)->format('H:i'),
                'end_time' => Carbon::parse($row->end_time)->format('H:i'),
                'room_name' => $row->room->name ?? '-',
                'coffee_break' => (bool) $row->coffee_break,
                'sonorizacao' => (bool) $row->sonorizacao,
                'transmissao' => (bool) $row->transmissao,
            ];
        }
    }

    public function previousMonth(): void
    {
        if ($this->currentMonth === 1) {
            $this->currentMonth = 12;
            $this->currentYear--;
        } else {
            $this->currentMonth--;
        }
        $this->selectedDate = '';
        $this->dayReservations = [];
        $this->loadReservations();
    }

    public function nextMonth(): void
    {
        if ($this->currentMonth === 12) {
            $this->currentMonth = 1;
            $this->currentYear++;
        } else {
            $this->currentMonth++;
        }
        $this->selectedDate = '';
        $this->dayReservations = [];
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
        return Carbon::create($this->currentYear, $this->currentMonth, 1)->translatedFormat('F \\d\\e Y');
    }

    public function getCalendarDaysProperty(): array
    {
        $start = Carbon::create($this->currentYear, $this->currentMonth, 1);
        $daysInMonth = $start->daysInMonth;
        $startDay = $start->dayOfWeek;

        $days = [];
        for ($i = 0; $i < $startDay; $i++) {
            $days[] = null;
        }
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $days[] = $d;
        }
        return $days;
    }

    public function render()
    {
        return view('livewire.admin.admin-calendar');
    }
}
