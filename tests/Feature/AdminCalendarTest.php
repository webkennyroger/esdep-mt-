<?php

use App\Livewire\Admin\AdminCalendar;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->superAdmin()->create());
});

test('month name starts with an uppercase letter', function () {
    Livewire::test(AdminCalendar::class)
        ->set('currentMonth', 9)
        ->set('currentYear', 2026)
        ->assertSee('Setembro de 2026')
        ->assertDontSee('setembro de 2026');
});

test('selected date label capitalizes the month name', function () {
    Livewire::test(AdminCalendar::class)
        ->call('selectDate', '2026-09-02')
        ->assertSee('de Setembro de 2026')
        ->assertDontSee('de setembro de 2026');
});
