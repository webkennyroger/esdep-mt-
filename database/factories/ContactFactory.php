<?php

namespace Database\Factories;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class ContactFactory extends Factory
{
    protected $model = Contact::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'cpf' => $this->faker->numerify('###.###.###-##'),
            'tipo' => $this->faker->randomElement(['Defensor', 'Diretor', 'Coordenador', 'Gerente', 'Estagiário']),
            'setor' => null,
            'is_active' => true,
            'subscribed_at' => now(),
        ];
    }
}
