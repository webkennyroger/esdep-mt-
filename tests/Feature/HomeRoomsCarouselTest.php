<?php

use App\Models\Room;

test('home page shows real active rooms instead of hardcoded ones', function () {
    Room::create(['name' => 'Sala Impacto Real', 'code' => 'IMP-01', 'capacity' => 42, 'theme_color' => '#2563eb', 'is_active' => true]);
    Room::create(['name' => 'Sala Inativa Nao Deve Aparecer', 'code' => 'INA-01', 'capacity' => 5, 'theme_color' => '#2563eb', 'is_active' => false]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Sala Impacto Real')
        ->assertSee('42 PESSOAS')
        ->assertDontSee('Sala Inativa Nao Deve Aparecer')
        ->assertDontSee('Sala Horizonte')
        ->assertDontSee('Sala Ipê');
});

test('home page does not crash for a room with no theme color set', function () {
    Room::create(['name' => 'Sala Sem Cor', 'code' => 'SC-01', 'capacity' => 10, 'is_active' => true]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Sala Sem Cor');
});

test('home page shows a friendly message when there are no active rooms', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Nenhuma sala disponível no momento.');
});
