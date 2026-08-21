<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\ContactGroup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ContactSeeder extends Seeder
{
    public function run(): void
    {
        collect([
            ['group' => 'CDI', 'name' => 'Responsável CDI', 'email' => 'responsavel.cdi@esdep.mt.gov.br', 'setor' => 'CDI'],
            ['group' => 'INSTITUCIONAL', 'name' => 'Responsável Institucional', 'email' => 'responsavel.institucional@esdep.mt.gov.br', 'setor' => 'INSTITUCIONAL'],
            ['group' => 'OUVIDORIA', 'name' => 'Responsável Ouvidoria', 'email' => 'responsavel.ouvidoria@esdep.mt.gov.br', 'setor' => 'OUVIDORIA'],
        ])->each(function (array $data) {
            $groupName = $data['group'];
            unset($data['group']);

            $contact = Contact::updateOrCreate(['email' => $data['email']], $data + [
                'password' => Hash::make('TroqueEstaSenha!2026'),
                'is_active' => true,
                'autorizado_at' => now(),
                'subscribed_at' => now(),
            ]);

            $groupId = ContactGroup::where('name', $groupName)->value('id');
            $contact->groups()->syncWithoutDetaching([$groupId => ['is_responsible' => true]]);
        });

        $groupIds = ContactGroup::pluck('id');

        Contact::factory()->count(10)->create()->each(function (Contact $contact) use ($groupIds) {
            $contact->groups()->syncWithoutDetaching([$groupIds->random() => ['is_responsible' => (bool) random_int(0, 1)]]);
        });
    }
}
