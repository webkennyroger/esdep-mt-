<?php

namespace Database\Seeders;

use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ReservationSeeder extends Seeder
{
    public function run(): void
    {
        Carbon::setLocale('pt_BR');

        $rooms = Room::all();
        $users = User::where('role', 'user')->get();

        if ($rooms->isEmpty() || $users->isEmpty()) {
            $this->command->error('Execute RoomSeeder e UserSeeder primeiro.');
            return;
        }

        $horizonte = $rooms->firstWhere('code', 'HORIZONTE') ?? $rooms->first();
        $ipe = $rooms->firstWhere('code', 'IPE') ?? $rooms->last();

        $events = [
            [
                'event_name' => 'Encontro de Lideranças',
                'description' => 'Reunião de alinhamento das lideranças da instituição',
                'requester_name' => 'Maria Silva',
                'requester_email' => 'maria.silva@esdep.mt.gov.br',
                'room_id' => $horizonte->id,
                'date' => Carbon::tomorrow()->toDateString(),
                'start_time' => '09:00',
                'end_time' => '11:00',
                'attendees_count' => 45,
                'status' => 'authorized',
                'coffee_break' => true,
                'sonorizacao' => true,
                'transmissao' => false,
            ],
            [
                'event_name' => 'Capacitação em Direito Digital',
                'description' => 'Treinamento sobre novas legislações digitais',
                'requester_name' => 'João Santos',
                'requester_email' => 'joao.santos@esdep.mt.gov.br',
                'room_id' => $ipe->id,
                'date' => Carbon::tomorrow()->addDay()->toDateString(),
                'start_time' => '14:00',
                'end_time' => '17:00',
                'attendees_count' => 25,
                'status' => 'pending',
                'coffee_break' => true,
                'sonorizacao' => false,
                'transmissao' => true,
            ],
            [
                'event_name' => 'Workshop de Inteligência Artificial',
                'description' => 'Introdução ao uso de IA no setor público',
                'requester_name' => 'Ana Oliveira',
                'requester_email' => 'ana.oliveira@esdep.mt.gov.br',
                'room_id' => $horizonte->id,
                'date' => Carbon::tomorrow()->addDays(2)->toDateString(),
                'start_time' => '08:30',
                'end_time' => '12:30',
                'attendees_count' => 50,
                'status' => 'authorized',
                'coffee_break' => true,
                'sonorizacao' => true,
                'transmissao' => true,
            ],
            [
                'event_name' => 'Conselho de Administração',
                'description' => 'Reunião mensal do conselho',
                'requester_name' => 'Pedro Costa',
                'requester_email' => 'pedro.costa@esdep.mt.gov.br',
                'room_id' => $ipe->id,
                'date' => Carbon::tomorrow()->addDays(4)->toDateString(),
                'start_time' => '10:00',
                'end_time' => '12:00',
                'attendees_count' => 15,
                'status' => 'authorized',
                'coffee_break' => true,
                'sonorizacao' => false,
                'transmissao' => false,
            ],
            [
                'event_name' => 'Auditoria Interna',
                'description' => 'Revisão de processos e procedimentos',
                'requester_name' => 'Lucia Ferreira',
                'requester_email' => 'lucia.ferreira@esdep.mt.gov.br',
                'room_id' => $ipe->id,
                'date' => Carbon::tomorrow()->addDays(6)->toDateString(),
                'start_time' => '09:00',
                'end_time' => '11:30',
                'attendees_count' => 8,
                'status' => 'pending',
                'coffee_break' => false,
                'sonorizacao' => true,
                'transmissao' => false,
            ],
            [
                'event_name' => 'Palestra sobre Compliance',
                'description' => 'Palestra sobre conformidade e ética',
                'requester_name' => 'Carlos Souza',
                'requester_email' => 'carlos.souza@esdep.mt.gov.br',
                'room_id' => $horizonte->id,
                'date' => Carbon::tomorrow()->addDays(9)->toDateString(),
                'start_time' => '14:00',
                'end_time' => '16:00',
                'attendees_count' => 55,
                'status' => 'authorized',
                'coffee_break' => true,
                'sonorizacao' => true,
                'transmissao' => true,
            ],
            [
                'event_name' => 'Reunião de Equipe - TI',
                'description' => 'Alinhamento do time de tecnologia',
                'requester_name' => 'Fernanda Lima',
                'requester_email' => 'fernanda.lima@esdep.mt.gov.br',
                'room_id' => $ipe->id,
                'date' => Carbon::tomorrow()->addDays(11)->toDateString(),
                'start_time' => '08:00',
                'end_time' => '09:30',
                'attendees_count' => 10,
                'status' => 'pending',
                'coffee_break' => false,
                'sonorizacao' => false,
                'transmissao' => true,
            ],
            [
                'event_name' => 'Treinamento de Segurança da Informação',
                'description' => 'Capacitação em boas práticas de segurança',
                'requester_name' => 'Roberto Almeida',
                'requester_email' => 'roberto.almeida@esdep.mt.gov.br',
                'room_id' => $horizonte->id,
                'date' => Carbon::tomorrow()->addDays(14)->toDateString(),
                'start_time' => '13:00',
                'end_time' => '17:00',
                'attendees_count' => 40,
                'status' => 'rejected',
                'coffee_break' => true,
                'sonorizacao' => true,
                'transmissao' => false,
            ],
            [
                'event_name' => 'Apresentação de Resultados',
                'description' => 'Apresentação dos resultados do trimestre',
                'requester_name' => 'Juliana Ribeiro',
                'requester_email' => 'juliana.ribeiro@esdep.mt.gov.br',
                'room_id' => $horizonte->id,
                'date' => Carbon::tomorrow()->addDays(17)->toDateString(),
                'start_time' => '15:00',
                'end_time' => '17:00',
                'attendees_count' => 60,
                'status' => 'authorized',
                'coffee_break' => true,
                'sonorizacao' => true,
                'transmissao' => true,
            ],
            [
                'event_name' => 'Reunião de Acolhimento',
                'description' => 'Recepção e integração de novos colaboradores',
                'requester_name' => 'Marcos Pereira',
                'requester_email' => 'marcos.pereira@esdep.mt.gov.br',
                'room_id' => $ipe->id,
                'date' => Carbon::tomorrow()->addDays(19)->toDateString(),
                'start_time' => '09:00',
                'end_time' => '11:00',
                'attendees_count' => 20,
                'status' => 'pending',
                'coffee_break' => true,
                'sonorizacao' => false,
                'transmissao' => false,
            ],
        ];

        foreach ($events as $event) {
            Reservation::create($event);
        }

        $this->command->info('10 reservas criadas com sucesso com os campos do site.');
    }
}