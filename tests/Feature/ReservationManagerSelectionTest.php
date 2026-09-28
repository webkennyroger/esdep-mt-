<?php

use App\Livewire\Admin\ReservationManager;
use App\Models\Reservation;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->superAdmin()->create());
});

test('toggling select all selects every reservation on the current page', function () {
    $reservations = Reservation::factory()->count(3)->create();

    $component = Livewire::test(ReservationManager::class)
        ->set('selectAll', true);

    expect($component->get('selected'))->toEqualCanonicalizing($reservations->pluck('id')->all());

    $component->set('selectAll', false);

    expect($component->get('selected'))->toBe([]);
});

test('changing the status filter clears the current selection', function () {
    $reservation = Reservation::factory()->create();

    $component = Livewire::test(ReservationManager::class)
        ->set('selected', [$reservation->id]);

    expect($component->get('selected'))->toBe([$reservation->id]);

    $component->call('setFilter', 'pending');

    expect($component->get('selected'))->toBe([]);
});

test('changing the search term clears the current selection', function () {
    $reservation = Reservation::factory()->create();

    $component = Livewire::test(ReservationManager::class)
        ->set('selected', [$reservation->id]);

    $component->set('search', 'algo');

    expect($component->get('selected'))->toBe([]);
});

test('reservation search is case insensitive', function () {
    Reservation::factory()->create(['event_name' => 'Workshop de Direitos Humanos']);

    Livewire::test(ReservationManager::class)
        ->set('search', 'workshop')
        ->assertSee('Workshop de Direitos Humanos');

    Livewire::test(ReservationManager::class)
        ->set('search', 'WORKSHOP')
        ->assertSee('Workshop de Direitos Humanos');
});
