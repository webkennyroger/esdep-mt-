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
            ['name' => 'Marcos Oliveira',       'email' => 'marcos.oliveira@esdep.mt.gov.br',     'cpf' => '000.000.000-01', 'tipo' => 'Defensor',  'setor' => 'DJ',  'autorizado_at' => now(),  'group' => 'Defensores'],
            ['name' => 'Ana Paula Ribeiro',     'email' => 'ana.ribeiro@esdep.mt.gov.br',         'cpf' => '000.000.000-02', 'tipo' => 'Defensor',  'setor' => 'DJ',  'autorizado_at' => now(),  'group' => 'Defensores'],
            ['name' => 'Roberto Nascimento',    'email' => 'roberto.nascimento@esdep.mt.gov.br',   'cpf' => '000.000.000-03', 'tipo' => 'Defensor',  'setor' => 'DJ',  'autorizado_at' => now(),  'group' => 'Defensores'],
            ['name' => 'Fernanda Costa',        'email' => 'fernanda.costa@esdep.mt.gov.br',       'cpf' => '000.000.000-04', 'tipo' => 'Defensor',  'setor' => 'DJ',  'autorizado_at' => now(),  'group' => 'Defensores'],
            ['name' => 'Pedro Henrique Alves',  'email' => 'pedro.alves@esdep.mt.gov.br',          'cpf' => '000.000.000-05', 'tipo' => 'Defensor',  'setor' => 'DJ',  'autorizado_at' => now(),  'group' => 'Defensores'],

            // Servidores
            ['name' => 'Carlos Eduardo Silva',  'email' => 'carlos.silva@esdep.mt.gov.br',         'cpf' => '000.000.000-06', 'tipo' => 'Servidor',  'setor' => 'DGE', 'autorizado_at' => now(),  'group' => 'Servidores'],
            ['name' => 'Mariana Ferreira',      'email' => 'mariana.ferreira@esdep.mt.gov.br',     'cpf' => '000.000.000-07', 'tipo' => 'Servidor',  'setor' => 'CDI', 'autorizado_at' => now(),  'group' => 'Servidores'],
            ['name' => 'Lucas Martins',         'email' => 'lucas.martins@esdep.mt.gov.br',        'cpf' => '000.000.000-08', 'tipo' => 'Servidor',  'setor' => 'DGE', 'autorizado_at' => now(),  'group' => 'Servidores'],
            ['name' => 'Juliana Barbosa',       'email' => 'juliana.barbosa@esdep.mt.gov.br',      'cpf' => '000.000.000-09', 'tipo' => 'Servidor',  'setor' => 'CDI', 'autorizado_at' => now(),  'group' => 'Servidores'],
            ['name' => 'Ricardo Souza',         'email' => 'ricardo.souza@esdep.mt.gov.br',        'cpf' => '000.000.000-10', 'tipo' => 'Servidor',  'setor' => 'DGE', 'autorizado_at' => now(),  'group' => 'Servidores'],

            // Estagiarios
            ['name' => 'Gabriel Mendes',        'email' => 'gabriel.mendes@esdep.mt.gov.br',       'cpf' => '000.000.000-11', 'tipo' => 'Estagiario','setor' => 'CDI', 'autorizado_at' => null,  'group' => 'Estagiarios'],
            ['name' => 'Isabela Rodrigues',     'email' => 'isabela.rodrigues@esdep.mt.gov.br',    'cpf' => '000.000.000-12', 'tipo' => 'Estagiario','setor' => 'DGE', 'autorizado_at' => null,  'group' => 'Estagiarios'],
            ['name' => 'Thiago Almeida',        'email' => 'thiago.almeida@esdep.mt.gov.br',       'cpf' => '000.000.000-13', 'tipo' => 'Estagiario','setor' => 'CDI', 'autorizado_at' => null,  'group' => 'Estagiarios'],

            // Gestores
            ['name' => 'Diretor Geral',         'email' => 'diretor@esdep.mt.gov.br',              'cpf' => '000.000.000-14', 'tipo' => 'Gestor',    'setor' => 'DGDI','autorizado_at' => now(),  'group' => 'Gestores'],
            ['name' => 'Coordenador Pedagogico','email' => 'coord.pedagogico@esdep.mt.gov.br',     'cpf' => '000.000.000-15', 'tipo' => 'Gestor',    'setor' => 'DGE', 'autorizado_at' => now(),  'group' => 'Gestores'],
            ['name' => 'Gerente Administrativo','email' => 'gerente.admin@esdep.mt.gov.br',        'cpf' => '000.000.000-16', 'tipo' => 'Gestor',    'setor' => 'DGE', 'autorizado_at' => now(),  'group' => 'Gestores'],

            // Convidados
            ['name' => 'Dr. Joao Peres',        'email' => 'joao.peres@mp.mt.gov.br',              'cpf' => '000.000.000-17', 'tipo' => 'Convidado', 'setor' => null,  'autorizado_at' => null,  'group' => 'Convidados'],
            ['name' => 'Profa. Lucia Campos',   'email' => 'lucia.campos@ufmt.edu.br',             'cpf' => '000.000.000-18', 'tipo' => 'Convidado', 'setor' => null,  'autorizado_at' => null,  'group' => 'Convidados'],
            ['name' => 'Dr. Fernando Rezende',  'email' => 'fernando.rezende@tjmt.jus.br',         'cpf' => '000.000.000-19', 'tipo' => 'Convidado', 'setor' => null,  'autorizado_at' => null,  'group' => 'Convidados'],
        ];

        foreach ($contacts as $data) {
            $group = $data['group'];
            unset($data['group']);

            $contact = Contact::updateOrCreate(
                ['email' => $data['email']],
                $data
            );

            $groupModel = ContactGroup::where('name', $group)->first();
            if ($groupModel && !$contact->groups->contains($groupModel->id)) {
                $contact->groups()->attach($groupModel);
            }
        }
    }
}
