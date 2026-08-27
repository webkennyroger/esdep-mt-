<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = User::updateOrCreate(
            ['email' => 'superadmin@esdep.mt.gov.br'],
            ['name' => 'Super Admin', 'is_active' => true, 'password' => Hash::make('TroqueEstaSenha!2026')]
        );

        $superAdmin->syncRoles(['super-admin']);

        User::factory()->count(10)->admin()->create();
    }
}
