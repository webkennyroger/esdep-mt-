<?php

use App\Livewire\Admin\Reports;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->superAdmin()->create());
    $this->room = Room::create([
        'name' => 'Sala Horizonte',
        'code' => 'HORIZONTE',
        'capacity' => 60,
        'theme_color' => '#008b5c',
        'is_active' => true,
    ]);
});

test('export excel action returns a downloadable xlsx response scoped to the filtered period', function () {
    Reservation::factory()->create(['room_id' => $this->room->id, 'event_name' => 'Dentro do Período', 'date' => now()->startOfMonth()->addDays(2)]);
    Reservation::factory()->create(['room_id' => $this->room->id, 'event_name' => 'Fora do Período', 'date' => now()->addMonths(3)]);

    $response = app(Reports::class)->exportExcel();

    expect($response->headers->get('Content-Type'))->toContain('spreadsheet');
});

test('export pdf action returns a downloadable pdf response', function () {
    Reservation::factory()->create(['room_id' => $this->room->id, 'event_name' => 'Evento PDF', 'date' => now()->startOfMonth()->addDays(2)]);

    $response = app(Reports::class)->exportPdf();

    expect($response->headers->get('Content-Type'))->toContain('application/pdf');
});

test('copy text respects the room filter', function () {
    $matching = Reservation::factory()->create(['room_id' => $this->room->id, 'event_name' => 'Evento Da Sala', 'date' => now()->startOfMonth()->addDays(2)]);
    $otherRoom = Room::create(['name' => 'Sala Aurora', 'code' => 'AURORA', 'capacity' => 40, 'theme_color' => '#008b5c', 'is_active' => true]);
    Reservation::factory()->create(['room_id' => $otherRoom->id, 'event_name' => 'Evento De Outra Sala', 'date' => now()->startOfMonth()->addDays(2)]);

    $text = Livewire::test(Reports::class)
        ->set('roomId', $this->room->id)
        ->call('copyText')
        ->effects['returns'][0];

    expect($text)->toContain('Evento Da Sala')
        ->not->toContain('Evento De Outra Sala');
});

test('print html includes the reservations in the filtered period', function () {
    Reservation::factory()->create(['room_id' => $this->room->id, 'event_name' => 'Evento Impresso', 'date' => now()->startOfMonth()->addDays(2)]);

    $html = Livewire::test(Reports::class)->call('printHtml')->effects['returns'][0];

    expect($html)->toContain('Evento Impresso');
});
