<?php

namespace Database\Factories;

use App\Models\Room;
use App\Models\Reservation;
use App\Enums\ReservationStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    public function definition(): array
    {
        return [
            'room_id' => Room::factory(),
            'event_name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'requester_name' => $this->faker->name(),
            'requester_email' => $this->faker->safeEmail(),
            'date' => $this->faker->dateTimeBetween('+1 week', '+1 month'),
            'start_time' => '09:00',
            'end_time' => '11:00',
            'attendees_count' => $this->faker->numberBetween(1, 60),
            'status' => ReservationStatus::PENDING,
            'coffee_break' => false,
            'sonorizacao' => false,
            'transmissao' => false,
        ];
    }
}
