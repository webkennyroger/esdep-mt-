<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            RoomSeeder::class,
            ReservationSeeder::class,
            ContactGroupSeeder::class,
            ContactSeeder::class,
        ]);
    }
}
