<?php

use App\Models\Contact;
use App\Models\User;

test('sidebar "Nova Reserva" links to the dedicated create-reservation page', function () {
    $admin = User::factory()->superAdmin()->create();

    $response = $this->actingAs($admin)->get(route('dashboard'));

    $response->assertOk()
        ->assertSee('href="'.route('admin.reservations.create').'"', false)
        ->assertDontSee("window.dispatchEvent(new CustomEvent('openReservationCreator'))", false);
});

test('contact dashboard "Nova reserva" still links to their own booking page', function () {
    $contact = Contact::create(['name' => 'Gestor', 'email' => 'gestor@example.com', 'password' => 'password', 'is_active' => true]);

    $response = $this->actingAs($contact, 'contact')->get(route('contact.dashboard'));

    $response->assertOk()
        ->assertSee('href="'.route('contact.booking').'"', false)
        ->assertDontSee("window.dispatchEvent(new CustomEvent('openReservationCreator'))", false);
});
