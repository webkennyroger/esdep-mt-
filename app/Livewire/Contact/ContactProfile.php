<?php

namespace App\Livewire\Contact;

use App\Models\Reservation;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.contact', ['title' => 'Meu Perfil — ESDEP-MT'])]
class ContactProfile extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $passwordConfirmation = '';

    public function mount(): void
    {
        $this->name = auth('contact')->user()->name;
        $this->email = auth('contact')->user()->email;
    }

    public function save(): void
    {
        $contact = auth('contact')->user();
        $data = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('contacts')->ignore($contact->id)],
            'password' => ['nullable', 'string', 'min:8', 'same:passwordConfirmation'],
        ]);
        $newEmail = strtolower(trim($data['email']));
        DB::transaction(function () use ($contact, $data, $newEmail): void {
            $oldEmail = $contact->email;
            $contact->update(array_filter([
                'name' => $data['name'], 'email' => $newEmail,
                'password' => $data['password'] ?: null,
            ], fn ($value) => $value !== null));
            Reservation::whereRaw('LOWER(requester_email) = ?', [strtolower($oldEmail)])
                ->update(['requester_email' => $newEmail]);
        });
        $this->email = $newEmail;
        $this->reset(['password', 'passwordConfirmation']);
        $this->js("window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: 'Perfil atualizado com sucesso.' } }))");
    }

    public function render()
    {
        return view('livewire.contact.contact-profile');
    }
}
