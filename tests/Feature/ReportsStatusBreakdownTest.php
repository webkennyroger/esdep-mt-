<?php

use App\Livewire\Admin\Reports;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Livewire\Livewire;

test('status breakdown reports the percentage share of each reservation status', function () {
    $this->actingAs(User::factory()->superAdmin()->create());
    $room = Room::create([
        'name' => 'Sala Horizonte',
        'code' => 'HORIZONTE',
        'capacity' => 60,
        'theme_color' => '#008b5c',
        'is_active' => true,
    ]);

    $date = now()->startOfMonth()->addDays(2);
    Reservation::factory()->count(3)->create(['room_id' => $room->id, 'status' => 'authorized', 'date' => $date]);
    Reservation::factory()->create(['room_id' => $room->id, 'status' => 'pending', 'date' => $date]);

    $component = Livewire::test(Reports::class);
    $breakdown = $component->get('stats')['statusBreakdown'];

    expect($breakdown['approved']['value'])->toBe(3)
        ->and($breakdown['approved']['pct'])->toBe(75.0)
        ->and($breakdown['pending']['value'])->toBe(1)
        ->and($breakdown['pending']['pct'])->toBe(25.0)
        ->and($breakdown['rejected']['value'])->toBe(0)
        ->and($breakdown['rejected']['pct'])->toBe(0.0);
});
