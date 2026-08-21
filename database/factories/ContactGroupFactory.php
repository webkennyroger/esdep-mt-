<?php

namespace Database\Factories;

use App\Models\ContactGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactGroupFactory extends Factory
{
    protected $model = ContactGroup::class;

    public function definition(): array
    {
        return [
            'name' => strtoupper($this->faker->unique()->words(2, true)),
            'description' => $this->faker->sentence(),
            'color' => $this->faker->hexColor(),
            'owner_contact_id' => null,
        ];
    }
}
