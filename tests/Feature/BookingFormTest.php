<?php

namespace Tests\Feature;

use App\Models\Room;
use App\Models\User;
use App\Models\Reservation;
use App\Enums\ReservationStatus;
use Livewire\Livewire;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookingFormTest extends TestCase
{
    use RefreshDatabase;

    private Room $room;

    protected function setUp(): void
    {
        parent::setUp();
        $this->room = Room::factory()->create(['capacity' => 60]);
    }

    private function createAdmin(): User
    {
        return User::factory()->create([
            'email' => 'admin@esdep.mt.gov.br',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
    }

    public function test_submit_booking_saves_to_database(): void
    {
        $futureDate = now()->addDays(7)->format('Y-m-d');

        Livewire::test(\App\Livewire\Public\BookingForm::class)
            ->set('requesterName', 'Joao da Silva')
            ->set('requesterEmail', 'joao@exemplo.gov.br')
            ->set('eventName', 'Reuniao de Trabalho')
            ->set('roomId', (string) $this->room->id)
            ->set('date', $futureDate)
            ->set('startTime', '09:00')
            ->set('endTime', '11:00')
            ->set('attendeesCount', 25)
            ->set('coffeeBreak', true)
            ->set('sonorizacao', false)
            ->set('transmissao', true)
            ->call('submit')
            ->assertSet('showSuccess', true);

        $reservation = Reservation::latest()->first();
        $this->assertNotNull($reservation);
        $this->assertEquals('Reuniao de Trabalho', $reservation->event_name);
        $this->assertEquals('Joao da Silva', $reservation->requester_name);
        $this->assertEquals('joao@exemplo.gov.br', $reservation->requester_email);
        $this->assertEquals($this->room->id, $reservation->room_id);
        $this->assertEquals($futureDate, $reservation->date->format('Y-m-d'));
        $this->assertEquals('09:00', $reservation->start_time);
        $this->assertEquals('11:00', $reservation->end_time);
        $this->assertEquals(25, $reservation->attendees_count);
        $this->assertTrue($reservation->coffee_break);
        $this->assertFalse($reservation->sonorizacao);
        $this->assertTrue($reservation->transmissao);
        $this->assertEquals(ReservationStatus::PENDING, $reservation->status);
    }

    public function test_reservation_appears_in_admin_manager(): void
    {
        $futureDate = now()->addDays(10)->format('Y-m-d');

        $reservation = Reservation::factory()->create([
            'room_id' => $this->room->id,
            'event_name' => 'Workshop de Capacitacao',
            'requester_name' => 'Maria Santos',
            'requester_email' => 'maria@exemplo.gov.br',
            'date' => $futureDate,
            'start_time' => '14:00',
            'end_time' => '17:00',
            'attendees_count' => 40,
            'status' => ReservationStatus::PENDING,
        ]);

        $admin = $this->createAdmin();

        $this->actingAs($admin);

        Livewire::test(\App\Livewire\Admin\ReservationManager::class)
            ->assertSee('Workshop de Capacitacao')
            ->assertSee('Maria Santos');
    }

    public function test_all_services_saved_correctly(): void
    {
        $futureDate = now()->addDays(5)->format('Y-m-d');

        Livewire::test(\App\Livewire\Public\BookingForm::class)
            ->set('requesterName', 'Pedro Almeida')
            ->set('requesterEmail', 'pedro@exemplo.gov.br')
            ->set('eventName', 'Seminario de Direitos')
            ->set('roomId', (string) $this->room->id)
            ->set('date', $futureDate)
            ->set('startTime', '08:30')
            ->set('endTime', '12:30')
            ->set('attendeesCount', 50)
            ->set('coffeeBreak', true)
            ->set('sonorizacao', true)
            ->set('transmissao', false)
            ->call('submit')
            ->assertSet('showSuccess', true);

        $reservation = Reservation::latest()->first();
        $this->assertTrue($reservation->coffee_break);
        $this->assertTrue($reservation->sonorizacao);
        $this->assertFalse($reservation->transmissao);
    }

    public function test_admin_sees_pending_reservations(): void
    {
        $admin = $this->createAdmin();

        $reservation = Reservation::factory()->create([
            'room_id' => $this->room->id,
            'status' => ReservationStatus::PENDING,
            'event_name' => 'Reuniao Teste Visibilidade',
            'requester_name' => 'Carlos Teste',
        ]);

        $this->actingAs($admin);

        Livewire::test(\App\Livewire\Admin\ReservationManager::class)
            ->set('activeFilter', 'pending')
            ->assertSee('Reuniao Teste Visibilidade')
            ->assertSee('Carlos Teste')
            ->assertSee('Pendente');
    }

    public function test_admin_can_approve_reservation(): void
    {
        $admin = $this->createAdmin();

        $reservation = Reservation::factory()->create([
            'room_id' => $this->room->id,
            'status' => ReservationStatus::PENDING,
        ]);

        $this->actingAs($admin);

        Livewire::test(\App\Livewire\Admin\ReservationManager::class)
            ->call('openApproveModal', $reservation->id, $reservation->event_name)
            ->assertSet('showApproveModal', true)
            ->call('confirmApprove')
            ->assertSet('showApproveModal', false);

        $reservation->refresh();
        $this->assertEquals(ReservationStatus::AUTHORIZED, $reservation->status);
    }
}
