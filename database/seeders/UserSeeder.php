<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(['email' => 'superadmin@esdep.mt.gov.br'], [
            'name' => 'Super Admin',
            'password' => Hash::make('password'),
            'role' => 'super-admin',
        ]);

        User::firstOrCreate(['email' => 'admin@esdep.mt.gov.br'], [
            'name' => 'Admin',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::firstOrCreate(['email' => 'gerente@esdep.mt.gov.br'], [
            'name' => 'Gerente de Reservas',
            'password' => Hash::make('password'),
            'role' => 'manager',
        ]);

        $users = [
            ['name' => 'Maria Silva', 'email' => 'maria.silva@esdep.mt.gov.br'],
            ['name' => 'João Santos', 'email' => 'joao.santos@esdep.mt.gov.br'],
            ['name' => 'Ana Oliveira', 'email' => 'ana.oliveira@esdep.mt.gov.br'],
            ['name' => 'Pedro Costa', 'email' => 'pedro.costa@esdep.mt.gov.br'],
            ['name' => 'Lucia Ferreira', 'email' => 'lucia.ferreira@esdep.mt.gov.br'],
            ['name' => 'Carlos Souza', 'email' => 'carlos.souza@esdep.mt.gov.br'],
            ['name' => 'Fernanda Lima', 'email' => 'fernanda.lima@esdep.mt.gov.br'],
            ['name' => 'Roberto Almeida', 'email' => 'roberto.almeida@esdep.mt.gov.br'],
            ['name' => 'Juliana Ribeiro', 'email' => 'juliana.ribeiro@esdep.mt.gov.br'],
            ['name' => 'Marcos Pereira', 'email' => 'marcos.pereira@esdep.mt.gov.br'],
        ];

        foreach ($users as $user) {
            User::firstOrCreate(['email' => $user['email']], [
                'name' => $user['name'],
                'password' => Hash::make('password'),
                'role' => 'user',
            ]);
        }

        $this->command->info('13 usuarios criados com sucesso.');
    }
}
