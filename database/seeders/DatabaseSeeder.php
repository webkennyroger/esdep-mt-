<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\ContactGroup;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = User::updateOrCreate(
            ['email' => 'superadmin@esdep.mt.gov.br'],
            ['name' => 'Super Admin', 'role' => 'super-admin', 'password' => Hash::make('TroqueEstaSenha!2026')]
        );

        $groups = collect([
            ['name' => 'ESDEP', 'description' => 'Gestão dos espaços da ESDEP', 'color' => '#008b5c'],
            ['name' => 'INSTITUCIONAL', 'description' => 'Gestão dos espaços institucionais', 'color' => '#2563eb'],
            ['name' => 'OUVIDORIA', 'description' => 'Gestão dos espaços da Ouvidoria', 'color' => '#7c3aed'],
        ])->mapWithKeys(function (array $data) {
            $group = ContactGroup::updateOrCreate(['name' => $data['name']], $data);

            return [$data['name'] => $group];
        });

        $rooms = collect([
            ['group' => 'ESDEP', 'name' => 'Sala Horizonte', 'code' => 'HORIZONTE', 'capacity' => 60, 'theme_color' => '#008b5c'],
            ['group' => 'ESDEP', 'name' => 'Sala Ipê', 'code' => 'IPE', 'capacity' => 30, 'theme_color' => '#059669'],
            ['group' => 'INSTITUCIONAL', 'name' => 'Auditório Sede', 'code' => 'SEDE-AUD', 'capacity' => 350, 'theme_color' => '#2563eb'],
            ['group' => 'OUVIDORIA', 'name' => 'Sala 165', 'code' => 'SALA-165', 'capacity' => 10, 'theme_color' => '#7c3aed'],
        ])->mapWithKeys(function (array $data) use ($groups) {
            $groupName = $data['group'];
            unset($data['group']);
            $room = Room::updateOrCreate(['code' => $data['code']], $data + [
                'contact_group_id' => $groups[$groupName]->id,
                'is_active' => true,
            ]);

            return [$data['code'] => $room];
        });

        $contacts = collect([
            ['group' => 'ESDEP', 'name' => 'Responsável ESDEP', 'email' => 'responsavel.esdep@esdep.mt.gov.br', 'setor' => 'ESDEP'],
            ['group' => 'INSTITUCIONAL', 'name' => 'Responsável Institucional', 'email' => 'responsavel.institucional@esdep.mt.gov.br', 'setor' => 'INSTITUCIONAL'],
            ['group' => 'OUVIDORIA', 'name' => 'Responsável Ouvidoria', 'email' => 'responsavel.ouvidoria@esdep.mt.gov.br', 'setor' => 'OUVIDORIA'],
        ])->mapWithKeys(function (array $data) use ($groups) {
            $groupName = $data['group'];
            unset($data['group']);
            $contact = Contact::updateOrCreate(['email' => $data['email']], $data + [
                'password' => Hash::make('TroqueEstaSenha!2026'),
                'is_active' => true,
                'autorizado_at' => now(),
                'subscribed_at' => now(),
            ]);
            $contact->groups()->syncWithoutDetaching([
                $groups[$groupName]->id => ['is_responsible' => true],
            ]);

            return [$groupName => $contact];
        });

        Reservation::updateOrCreate(['protocol' => 'RES-DEMO-0001'], [
            'room_id' => $rooms['HORIZONTE']->id,
            'event_name' => 'Evento demonstrativo ESDEP',
            'description' => 'Reserva criada para validar o fluxo do grupo responsável.',
            'requester_name' => $contacts['ESDEP']->name,
            'requester_email' => $contacts['ESDEP']->email,
            'date' => now()->addDays(7)->toDateString(),
            'start_time' => '09:00',
            'end_time' => '11:00',
            'attendees_count' => 30,
            'status' => 'pending',
            'terms_accepted' => true,
            'terms_accepted_at' => now(),
        ]);

        $this->command->warn('Dados de demonstração criados. Troque imediatamente as senhas do Super Admin e dos responsáveis.');
    }
}
