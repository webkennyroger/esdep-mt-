<?php

use App\Actions\CreateReservationAction;
use App\Actions\SendEventAnnouncementAction;
use App\Enums\ReservationStatus;
use App\Livewire\Admin\RoomManager;
use App\Mail\ReservationApprovedMail;
use App\Mail\ReservationRejectedMail;
use App\Mail\ReservationRequestedMail;
use App\Models\ContactGroup;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

function reservationRequestData(int $roomId): array
{
    return [
        'room_id' => $roomId,
        'event_name' => 'Evento de teste',
        'requester_name' => 'Solicitante Teste',
        'requester_email' => 'solicitante@example.com',
        'date' => now()->addDays(3)->toDateString(),
        'start_time' => '09:00',
        'end_time' => '10:00',
        'attendees_count' => 5,
    ];
}

test('new request notification goes to the room owner email when set', function () {
    Mail::fake();

    $room = Room::factory()->create(['notification_email' => 'dono-da-sala@example.com']);

    app(CreateReservationAction::class)->execute(reservationRequestData($room->id));

    Mail::assertQueued(ReservationRequestedMail::class, fn (ReservationRequestedMail $mail) => $mail->hasTo('dono-da-sala@example.com'));
});

test('new request notification falls back to the group email when the room has none', function () {
    Mail::fake();

    $group = ContactGroup::create(['name' => 'GRUPO NOTIFICACAO', 'color' => '#008b5c', 'notification_email' => 'dono-do-grupo@example.com']);
    $room = Room::factory()->create(['notification_email' => null, 'contact_group_id' => $group->id]);

    app(CreateReservationAction::class)->execute(reservationRequestData($room->id));

    Mail::assertQueued(ReservationRequestedMail::class, fn (ReservationRequestedMail $mail) => $mail->hasTo('dono-do-grupo@example.com'));
});

test('new request notification falls back to the default admin email when neither room nor group has one', function () {
    Mail::fake();

    $room = Room::factory()->create(['notification_email' => null, 'contact_group_id' => null]);

    app(CreateReservationAction::class)->execute(reservationRequestData($room->id));

    Mail::assertQueued(ReservationRequestedMail::class, fn (ReservationRequestedMail $mail) => $mail->hasTo(config('app.admin_email', 'admin@esdep.mt.gov.br')));
});

test('new request notification goes to every email when a room lists more than one, comma separated', function () {
    Mail::fake();

    $room = Room::factory()->create(['notification_email' => 'setor1@example.com, setor2@example.com,setor3@example.com']);

    app(CreateReservationAction::class)->execute(reservationRequestData($room->id));

    Mail::assertQueued(ReservationRequestedMail::class, function (ReservationRequestedMail $mail) {
        return $mail->hasTo('setor1@example.com')
            && $mail->hasTo('setor2@example.com')
            && $mail->hasTo('setor3@example.com');
    });
});

test('approval notification also copies the room owner email', function () {
    Mail::fake();

    $room = Room::factory()->create(['notification_email' => 'dono-da-sala@example.com']);
    $reservation = Reservation::factory()->create([
        'room_id' => $room->id,
        'status' => ReservationStatus::AUTHORIZED,
        'requester_email' => 'solicitante@example.com',
    ]);
    $admin = User::factory()->superAdmin()->create();

    app(SendEventAnnouncementAction::class)->execute($reservation, ['recipient_type' => 'contacts', 'contact_ids' => []], $admin);

    Mail::assertQueued(ReservationApprovedMail::class, fn (ReservationApprovedMail $mail) => $mail->hasTo('solicitante@example.com') && $mail->hasCc('dono-da-sala@example.com'));
});

test('rejection notification also copies the group owner email when the room has none', function () {
    Mail::fake();

    $group = ContactGroup::create(['name' => 'GRUPO REJEICAO', 'color' => '#008b5c', 'notification_email' => 'dono-do-grupo@example.com']);
    $room = Room::factory()->create(['notification_email' => null, 'contact_group_id' => $group->id]);
    $reservation = Reservation::factory()->create([
        'room_id' => $room->id,
        'status' => ReservationStatus::REJECTED,
        'requester_email' => 'solicitante@example.com',
    ]);
    $admin = User::factory()->superAdmin()->create();

    app(SendEventAnnouncementAction::class)->execute($reservation, ['recipient_type' => 'contacts', 'contact_ids' => []], $admin);

    Mail::assertQueued(ReservationRejectedMail::class, fn (ReservationRejectedMail $mail) => $mail->hasTo('solicitante@example.com') && $mail->hasCc('dono-do-grupo@example.com'));
});

test('multiple-email field rejects a list containing an invalid address', function () {
    $admin = User::factory()->superAdmin()->create();

    Livewire::actingAs($admin)->test(RoomManager::class)
        ->call('openCreateModal')
        ->set('name', 'Sala Validacao')
        ->set('notificationEmail', 'valido@example.com, isso-nao-e-email')
        ->call('createRoom')
        ->assertHasErrors(['notificationEmail']);
});
