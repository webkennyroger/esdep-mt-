<?php

use App\Livewire\Admin\ReservationManager;
use App\Mail\EventAnnouncementMail;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

function announceTestReservation(): Reservation
{
    $room = Room::create(['name' => 'Sala Divulgação', 'code' => 'DIV-01', 'capacity' => 20, 'is_active' => true]);

    return Reservation::create([
        'room_id' => $room->id,
        'event_name' => 'Evento para Divulgar',
        'requester_name' => 'Solicitante',
        'requester_email' => 'solicitante@example.com',
        'date' => now()->addDays(2)->toDateString(),
        'start_time' => '09:00',
        'end_time' => '10:00',
        'attendees_count' => 5,
        'status' => 'authorized',
    ]);
}

test('admin can announce an event to a free-text email with no contact record', function () {
    Mail::fake();
    $admin = User::factory()->superAdmin()->create();
    $reservation = announceTestReservation();

    Livewire::actingAs($admin)->test(ReservationManager::class)
        ->call('openAnnounceModal', $reservation->id, $reservation->event_name)
        ->set('announceExtraEmails', 'convidado-avulso@example.com')
        ->call('sendAnnouncement')
        ->assertHasNoErrors();

    Mail::assertSent(EventAnnouncementMail::class, fn (EventAnnouncementMail $mail) => $mail->hasTo('convidado-avulso@example.com'));
});

test('announce modal rejects an invalid free-text email', function () {
    $admin = User::factory()->superAdmin()->create();
    $reservation = announceTestReservation();

    Livewire::actingAs($admin)->test(ReservationManager::class)
        ->call('openAnnounceModal', $reservation->id, $reservation->event_name)
        ->set('announceExtraEmails', 'nao-e-um-email')
        ->call('sendAnnouncement')
        ->assertHasErrors(['announceExtraEmails']);
});

test('announcement to a contact is actually delivered even without a queue worker', function () {
    config(['queue.default' => 'database']);
    Mail::fake();
    $admin = User::factory()->superAdmin()->create();
    $reservation = announceTestReservation();
    $contact = \App\Models\Contact::create(['name' => 'Convidado', 'email' => 'convidado@example.com', 'password' => 'password', 'is_active' => true]);

    Livewire::actingAs($admin)->test(ReservationManager::class)
        ->call('openAnnounceModal', $reservation->id, $reservation->event_name)
        ->set('announceRecipientType', 'contacts')
        ->set('announceSelectedContactIds', [$contact->id])
        ->call('sendAnnouncement')
        ->assertHasNoErrors();

    Mail::assertSent(EventAnnouncementMail::class, fn (EventAnnouncementMail $mail) => $mail->hasTo('convidado@example.com'));
});
