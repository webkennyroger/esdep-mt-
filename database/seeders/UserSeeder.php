<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'superadmin@esdep.mt.gov.br'],
            ['name' => 'Super Admin', 'role' => 'super-admin', 'password' => Hash::make('TroqueEstaSenha!2026')]
        );

        User::factory()->count(10)->create(['role' => 'admin']);
    }
}
