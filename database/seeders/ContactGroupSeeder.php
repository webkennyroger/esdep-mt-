<?php

namespace Database\Seeders;

use App\Models\ContactGroup;
use Illuminate\Database\Seeder;

class ContactGroupSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            ['name' => 'ESDEP', 'description' => 'Escola Superior da Defensoria Pública do Estado de Mato Grosso', 'color' => '#0066aa'],
            ['name' => 'CDI',  'description' => 'Coordenardoria de Desenvolvimento e Inovação', 'color' => '#059669'],
            ['name' => 'DGE',  'description' => 'Diretoria de Gestão Estratégica', 'color' => '#2563eb'],
            ['name' => 'DJ', 'description' => 'Diretoria Jurídica',  'color' => '#d97706'],
            ['name' => 'INTITUCIONAL',    'description' => 'Institucional/Geral',           'color' => '#7c3aed'],
            ['name' => 'OUVIDORIA',  'description' => 'Ouvidoria',   'color' => '#dc2626'],
        ];

        foreach ($groups as $group) {
            ContactGroup::updateOrCreate(
                ['name' => $group['name']],
                $group
            );
        }
    }
}
