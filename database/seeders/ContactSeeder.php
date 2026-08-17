<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\ContactGroup;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    public function run(): void
    {
        $contacts = [
            // Defensores
            ['name' => 'Marcos Oliveira',       'email' => 'marcos.oliveira@esdep.mt.gov.br',     'group' => 'Defensores'],
            ['name' => 'Ana Paula Ribeiro',     'email' => 'ana.ribeiro@esdep.mt.gov.br',         'group' => 'Defensores'],
            ['name' => 'Roberto Nascimento',    'email' => 'roberto.nascimento@esdep.mt.gov.br',   'group' => 'Defensores'],
            ['name' => 'Fernanda Costa',        'email' => 'fernanda.costa@esdep.mt.gov.br',       'group' => 'Defensores'],
            ['name' => 'Pedro Henrique Alves',  'email' => 'pedro.alves@esdep.mt.gov.br',          'group' => 'Defensores'],

            // Servidores
            ['name' => 'Carlos Eduardo Silva',  'email' => 'carlos.silva@esdep.mt.gov.br',         'group' => 'Servidores'],
            ['name' => 'Mariana Ferreira',      'email' => 'mariana.ferreira@esdep.mt.gov.br',     'group' => 'Servidores'],
            ['name' => 'Lucas Martins',         'email' => 'lucas.martins@esdep.mt.gov.br',        'group' => 'Servidores'],
            ['name' => 'Juliana Barbosa',       'email' => 'juliana.barbosa@esdep.mt.gov.br',      'group' => 'Servidores'],
            ['name' => 'Ricardo Souza',         'email' => 'ricardo.souza@esdep.mt.gov.br',        'group' => 'Servidores'],

            // Estagiarios
            ['name' => 'Gabriel Mendes',        'email' => 'gabriel.mendes@esdep.mt.gov.br',       'group' => 'Estagiarios'],
            ['name' => 'Isabela Rodrigues',     'email' => 'isabela.rodrigues@esdep.mt.gov.br',    'group' => 'Estagiarios'],
            ['name' => 'Thiago Almeida',        'email' => 'thiago.almeida@esdep.mt.gov.br',       'group' => 'Estagiarios'],

            // Gestores
            ['name' => 'Diretor Geral',         'email' => 'diretor@esdep.mt.gov.br',              'group' => 'Gestores'],
            ['name' => 'Coordenador Pedagogico','email' => 'coord.pedagogico@esdep.mt.gov.br',     'group' => 'Gestores'],
            ['name' => 'Gerente Administrativo','email' => 'gerente.admin@esdep.mt.gov.br',        'group' => 'Gestores'],

            // Convidados
            ['name' => 'Dr. Joao Peres',        'email' => 'joao.peres@mp.mt.gov.br',              'group' => 'Convidados'],
            ['name' => 'Profa. Lucia Campos',   'email' => 'lucia.campos@ufmt.edu.br',             'group' => 'Convidados'],
            ['name' => 'Dr. Fernando Rezende',  'email' => 'fernando.rezende@tjmt.jus.br',         'group' => 'Convidados'],
        ];

        foreach ($contacts as $data) {
            $group = $data['group'];
            unset($data['group']);

            $contact = Contact::updateOrCreate(
                ['email' => $data['email']],
                array_merge($data, [
                    'is_active' => true,
                    'subscribed_at' => now(),
                ])
            );

            $groupModel = ContactGroup::where('name', $group)->first();
            if ($groupModel && !$contact->groups->contains($groupModel->id)) {
                $contact->groups()->attach($groupModel);
            }
        }
    }
}
