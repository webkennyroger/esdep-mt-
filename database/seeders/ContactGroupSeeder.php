<?php

namespace Database\Seeders;

use App\Models\ContactGroup;
use Illuminate\Database\Seeder;

class ContactGroupSeeder extends Seeder
{
    public function run(): void
    {
        collect([
            ['name' => 'CDI', 'description' => 'Gestão dos espaços do CDI', 'color' => '#008b5c'],
            ['name' => 'INSTITUCIONAL', 'description' => 'Gestão dos espaços institucionais', 'color' => '#2563eb'],
            ['name' => 'OUVIDORIA', 'description' => 'Gestão dos espaços da Ouvidoria', 'color' => '#7c3aed'],
        ])->each(fn (array $data) => ContactGroup::updateOrCreate(['name' => $data['name']], $data));

        ContactGroup::factory()->count(10)->create();
    }
}
