<?php

use App\Models\Contact;
use App\Models\ContactGroup;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Spatie\Permission\Models\Role;

test('o seeder garante a estrutura de papeis e permissoes', function () {
    $this->seed(DatabaseSeeder::class);

    expect(Role::where('name', 'admin')->exists())->toBeTrue()
        ->and(Role::findByName('super-admin', 'web')->permissions->pluck('name')->sort()->values()->all())
        ->toBe(['access-activity-log', 'access-contacts', 'access-reports', 'manage-users']);
});

test('o seeder nao cria dado de negocio algum', function () {
    // Salas, grupos, contatos e reservas sao cadastrados pela interface. Um
    // banco recem-semeado precisa nascer vazio, sem massa de demonstracao.
    $this->seed(DatabaseSeeder::class);

    expect(User::count())->toBe(0)
        ->and(Room::count())->toBe(0)
        ->and(ContactGroup::count())->toBe(0)
        ->and(Contact::count())->toBe(0)
        ->and(Reservation::count())->toBe(0);
});
