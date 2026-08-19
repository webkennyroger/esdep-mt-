<?php

namespace App\Livewire\Contact;

use App\Models\Reservation;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app', ['title' => 'Painel do Contato — ESDEP-MT'])]
class ContactDashboard extends Component
{
    public function getReservationsProperty()
    {
        $contact = auth('contact')->user();

        return Reservation::where('requester_email', $contact->email)
            ->with(['room'])
            ->orderBy('date', 'desc')
            ->get();
    }

    public function canEdit(Reservation $reservation): bool
    {
        $contact = auth('contact')->user();

        return strtolower($reservation->requester_email) === strtolower($contact->email);
    }

    public function render()
    {
        return view('livewire.contact.contact-dashboard');
    }
}
