<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'is_active' => true,
            'activated_at' => now(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Administrador: gerencia salas, reservas e calendario.
     */
    public function admin(): static
    {
        return $this->afterCreating(fn (User $user) => $user->assignRole('admin'));
    }

    /**
     * Super-administrador: tudo do admin, mais contatos, relatorios, historico
     * de atividades e gestao de usuarios.
     */
    public function superAdmin(): static
    {
        return $this->afterCreating(fn (User $user) => $user->assignRole('super-admin'));
    }

    /**
     * Conta provisionada pelo SSO que ainda aguarda liberacao de um admin.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
            'activated_at' => null,
            'password' => null,
            'keycloak_id' => fake()->uuid(),
        ]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
