<?php

use App\Models\Contact;
use App\Models\User;

test('sidebar "Nova Reserva" opens the quick-create modal instead of navigating to the public form', function () {
    $admin = User::factory()->superAdmin()->create();

    $response = $this->actingAs($admin)->get(route('dashboard'));

    $response->assertOk()
        // O botao dispara o mesmo evento de navegador usado pelo botao da pagina
        // "Gerenciar Reservas"; nao deve mais ser um <a> para reservations.create.
        ->assertSee("window.dispatchEvent(new CustomEvent('openReservationCreator'))", false)
        ->assertDontSee('href="'.route('reservations.create').'"', false);

    // O componente que escuta esse evento agora esta disponivel em qualquer
    // pagina do layout admin, nao so em "Gerenciar Reservas".
    $response->assertSeeLivewire('admin.reservation-editor');
});

test('contact dashboard "Nova reserva" still links to their own booking page', function () {
    $contact = Contact::create(['name' => 'Gestor', 'email' => 'gestor@example.com', 'password' => 'password', 'is_active' => true]);

    $response = $this->actingAs($contact, 'contact')->get(route('contact.dashboard'));

    $response->assertOk()
        ->assertSee('href="'.route('contact.booking').'"', false)
        ->assertDontSee("window.dispatchEvent(new CustomEvent('openReservationCreator'))", false);
});
