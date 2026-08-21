<?php

namespace Database\Factories;

use App\Models\Reservation;
use App\Models\ReservationLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationLogFactory extends Factory
{
    protected $model = ReservationLog::class;

    public function definition(): array
    {
        return [
            'reservation_id' => Reservation::factory(),
            'user_id' => null,
            'action' => 'created',
            'changes' => null,
            'reason' => $this->faker->sentence(),
        ];
    }
}
