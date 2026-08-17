<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Room;
use App\Models\Reservation;
use App\Enums\ReservationStatus;
use Livewire\Livewire;
use App\Livewire\Public\BookingForm;
use App\Livewire\Admin\ReservationEditor;
use App\Livewire\Admin\ReservationManager;
use App\Livewire\Admin\AdminCalendar;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReservationFlowTest extends TestCase
{
    use RefreshDatabase;

    private function createRoom(): Room
    {
        return Room::create([
            'name' => 'Sala Horizonte',
            'code' => 'HORIZONTE',
            'capacity' => 60,
            'theme_color' => '#008b5c',
            'description' => 'Sala de reuniões',
            'is_active' => true,
        ]);
    }

    public function test_public_booking_form_saves_all_fields_to_database(): void
    {
        $room = $this->createRoom();
        $date = now()->addDays(2)->toDateString();

        Livewire::test(BookingForm::class)
            ->set('roomId', $room->id)
            ->set('eventName', 'Seminário de Direito')
            ->set('description', 'Evento institucional importante')
            ->set('requesterName', 'Carlos Souza')
            ->set('requesterEmail', 'carlos@esdep.mt.gov.br')
            ->set('date', $date)
            ->set('startTime', '14:00')
            ->set('endTime', '16:00')
            ->set('attendeesCount', 40)
            ->set('coffeeBreak', true)
            ->set('sonorizacao', true)
            ->set('transmissao', true)
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('showSuccess', true);

        $saved = Reservation::first();
        $this->assertNotNull($saved);
        $this->assertEquals($room->id, $saved->room_id);
        $this->assertEquals('Seminário de Direito', $saved->event_name);
        $this->assertEquals('Evento institucional importante', $saved->description);
        $this->assertEquals('Carlos Souza', $saved->requester_name);
        $this->assertEquals('carlos@esdep.mt.gov.br', $saved->requester_email);
        $this->assertEquals($date, $saved->date->toDateString());
        $this->assertEquals('14:00', \Carbon\Carbon::parse($saved->start_time)->format('H:i'));
        $this->assertEquals('16:00', \Carbon\Carbon::parse($saved->end_time)->format('H:i'));
        $this->assertEquals(40, $saved->attendees_count);
        $this->assertTrue($saved->coffee_break);
        $this->assertTrue($saved->sonorizacao);
        $this->assertTrue($saved->transmissao);
        $this->assertEquals(ReservationStatus::PENDING, $saved->status);
    }

    public function test_admin_reservation_editor_updates_all_fields(): void
    {
        $room = $this->createRoom();
        $date = now()->addDays(3)->toDateString();

        $reservation = Reservation::create([
            'room_id' => $room->id,
            'event_name' => 'Evento Antigo',
            'description' => 'Desc Antiga',
            'requester_name' => 'Ana',
            'requester_email' => 'ana@esdep.mt.gov.br',
            'date' => $date,
            'start_time' => '10:00',
            'end_time' => '11:00',
            'attendees_count' => 10,
            'coffee_break' => false,
            'sonorizacao' => false,
            'transmissao' => false,
            'status' => 'pending',
        ]);

        Livewire::test(ReservationEditor::class)
            ->call('open', $reservation->id)
            ->assertSet('eventName', 'Evento Antigo')
            ->assertSet('coffeeBreak', false)
            ->set('eventName', 'Evento Atualizado')
            ->set('coffeeBreak', true)
            ->set('sonorizacao', true)
            ->set('transmissao', true)
            ->set('attendeesCount', 25)
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('showModal', false);

        $fresh = $reservation->fresh();
        $this->assertEquals('Evento Atualizado', $fresh->event_name);
        $this->assertTrue($fresh->coffee_break);
        $this->assertTrue($fresh->sonorizacao);
        $this->assertTrue($fresh->transmissao);
        $this->assertEquals(25, $fresh->attendees_count);
    }

    public function test_admin_approves_and_reservation_appears_in_calendar(): void
    {
        $room = $this->createRoom();
        $date = now()->addDays(5)->toDateString();

        $reservation = Reservation::create([
            'room_id' => $room->id,
            'event_name' => 'Reunião de Diretoria',
            'description' => 'Pauta estratégica',
            'requester_name' => 'Diretor Roberto',
            'requester_email' => 'roberto@esdep.mt.gov.br',
            'date' => $date,
            'start_time' => '09:00',
            'end_time' => '11:00',
            'attendees_count' => 15,
            'coffee_break' => true,
            'sonorizacao' => true,
            'transmissao' => false,
            'status' => 'pending',
        ]);

        // Approve reservation
        Livewire::test(ReservationManager::class)
            ->call('openApproveModal', $reservation->id, $reservation->event_name)
            ->call('confirmApprove')
            ->assertDispatched('reservationApproved');

        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => 'authorized',
        ]);

        // Check in AdminCalendar
        $calendar = Livewire::test(AdminCalendar::class);
        $calendar->call('loadReservations');

        $reservations = $calendar->get('reservations');
        $this->assertArrayHasKey($date, $reservations);
        $this->assertEquals('Reunião de Diretoria', $reservations[$date][0]['event_name']);
        $this->assertTrue($reservations[$date][0]['coffee_break']);
        $this->assertTrue($reservations[$date][0]['sonorizacao']);
        $this->assertFalse($reservations[$date][0]['transmissao']);
    }
}
