<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            UserSeeder::class,
            ContactGroupSeeder::class,
            RoomSeeder::class,
            ContactSeeder::class,
            ReservationSeeder::class,
        ]);

        $this->command->warn('Dados de demonstração criados. Troque imediatamente as senhas do Super Admin e dos responsáveis.');
    }
}
