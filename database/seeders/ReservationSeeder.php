<?php

namespace Database\Seeders;

use App\Enums\ReservationStatus;
use App\Models\Contact;
use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Database\Seeder;

class ReservationSeeder extends Seeder
{
    public function run(): void
    {
        $cdiEmail = 'responsavel.cdi@esdep.mt.gov.br';
        $cdiName = 'Responsável CDI';

        Reservation::updateOrCreate(['protocol' => 'RES-DEMO-0001'], [
            'room_id' => Room::where('code', 'HORIZONTE')->value('id'),
            'event_name' => 'Evento demonstrativo ESDEP',
            'description' => 'Reserva criada para validar o fluxo do grupo responsável.',
            'requester_name' => $cdiName,
            'requester_email' => $cdiEmail,
            'date' => now()->addDays(7)->toDateString(),
            'start_time' => '09:00',
            'end_time' => '11:00',
            'attendees_count' => 30,
            'status' => 'pending',
            'transmissao' => true,
            'sonorizacao' => true,
            'terms_accepted' => true,
            'terms_accepted_at' => now(),
        ]);

        Reservation::updateOrCreate(['protocol' => 'RES-DEMO-0002'], [
            'room_id' => Room::where('code', 'IPE')->value('id'),
            'event_name' => 'Oficina de Capacitação Institucional',
            'description' => 'Evento demonstrativo autorizado para validar calendários.',
            'requester_name' => $cdiName,
            'requester_email' => $cdiEmail,
            'date' => now()->addDays(8)->toDateString(),
            'start_time' => '14:00',
            'end_time' => '16:00',
            'attendees_count' => 20,
            'status' => 'authorized',
            'coffee_break' => true,
            'terms_accepted' => true,
            'terms_accepted_at' => now(),
        ]);

        Reservation::updateOrCreate(['protocol' => 'RES-DEMO-0003'], [
            'room_id' => Room::where('code', 'SEDE-AUD')->value('id'),
            'event_name' => 'Audiência Pública Transmitida',
            'description' => 'Evento demonstrativo com transmissão e Meet.',
            'requester_name' => 'Responsável Institucional',
            'requester_email' => 'responsavel.institucional@esdep.mt.gov.br',
            'date' => now()->addDays(10)->toDateString(),
            'start_time' => '09:00',
            'end_time' => '12:00',
            'attendees_count' => 120,
            'status' => 'authorized',
            'transmissao' => true,
            'sonorizacao' => true,
            'projetor' => true,
            'terms_accepted' => true,
            'terms_accepted_at' => now(),
        ]);

        Reservation::updateOrCreate(['protocol' => 'RES-DEMO-0004'], [
            'room_id' => Room::where('code', 'SALA-165')->value('id'),
            'event_name' => 'Reunião Não Autorizada',
            'description' => 'Evento demonstrativo rejeitado.',
            'requester_name' => 'Responsável Ouvidoria',
            'requester_email' => 'responsavel.ouvidoria@esdep.mt.gov.br',
            'date' => now()->addDays(12)->toDateString(),
            'start_time' => '10:00',
            'end_time' => '11:00',
            'attendees_count' => 8,
            'status' => 'rejected',
            'reject_reason' => 'Sala reservada para atividade institucional.',
            'terms_accepted' => true,
            'terms_accepted_at' => now(),
        ]);

        $roomIds = Room::pluck('id');
        $contacts = Contact::inRandomOrder()->limit(10)->get(['name', 'email']);
        $statuses = ReservationStatus::cases();

        for ($i = 1; $i <= 10; $i++) {
            $contact = $contacts[($i - 1) % $contacts->count()] ?? null;

            Reservation::factory()->create([
                'room_id' => $roomIds->random(),
                'requester_name' => $contact?->name ?? $cdiName,
                'requester_email' => $contact?->email ?? $cdiEmail,
                'date' => now()->addDays(14 + $i)->toDateString(),
                'status' => $statuses[array_rand($statuses)],
            ]);
        }
    }
}
