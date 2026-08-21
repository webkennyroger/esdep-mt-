<?php

namespace App\Livewire\Contact;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.contact', ['title' => 'Painel do Contato — ESDEP-MT'])]
class ContactDashboard extends Component
{
    protected $listeners = [
        'reservationCreated' => '$refresh',
        'reservationUpdated' => '$refresh',
        'reservationApproved' => '$refresh',
        'reservationRejected' => '$refresh',
    ];

    public function render()
    {
        $contact = auth('contact')->user();
        $reservations = Reservation::query()->whereIn('room_id', $contact->managedRoomIds());
        $statusCounts = (clone $reservations)->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        return view('livewire.admin.dashboard', [
            'roomsCount' => $contact->managedRoomIds()->count(),
            'pendingCount' => $statusCounts[ReservationStatus::PENDING->value] ?? 0,
            'authorizedCount' => $statusCounts[ReservationStatus::AUTHORIZED->value] ?? 0,
            'rejectedCount' => $statusCounts[ReservationStatus::REJECTED->value] ?? 0,
            'upcomingReservations' => (clone $reservations)->with('room')
                ->where('status', ReservationStatus::AUTHORIZED)
                ->where('date', '>=', now()->toDateString())
                ->orderBy('date')->orderBy('start_time')->limit(5)->get(),
            'pendingReservations' => (clone $reservations)->with('room')
                ->where('status', ReservationStatus::PENDING)
                ->latest()->limit(5)->get(),
        ]);
    }
}
