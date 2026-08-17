<?php

namespace Database\Seeders;

use App\Models\ContactGroup;
use Illuminate\Database\Seeder;

class ContactGroupSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            ['name' => 'Defensores',  'description' => 'Defensores Publicos do Estado',     'color' => '#059669'],
            ['name' => 'Servidores',  'description' => 'Servidores administrativos da ESDEP-MT', 'color' => '#2563eb'],
            ['name' => 'Estagiarios', 'description' => 'Estagiarios da Defensoria Publica',  'color' => '#d97706'],
            ['name' => 'Gestores',    'description' => 'Gestores e coordenadores',           'color' => '#7c3aed'],
            ['name' => 'Convidados',  'description' => 'Convidados externos para eventos',   'color' => '#dc2626'],
        ];

        foreach ($groups as $group) {
            ContactGroup::updateOrCreate(
                ['name' => $group['name']],
                $group
            );
        }
    }
}
