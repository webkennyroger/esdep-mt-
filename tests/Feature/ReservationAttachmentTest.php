<?php

use App\Livewire\Admin\ReservationEditor;
use App\Livewire\Admin\ReservationManager;
use App\Mail\ReservationApprovedMail;
use App\Mail\ReservationRejectedMail;
use App\Mail\ReservationRequestedMail;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->superAdmin()->create());
    $this->room = Room::create([
        'name' => 'Sala Horizonte',
        'code' => 'HORIZONTE',
        'capacity' => 60,
        'theme_color' => '#008b5c',
        'is_active' => true,
    ]);
});

// ─── Model helpers ─────────────────────────────────

test('reservation without attachment reports no attachment', function () {
    $reservation = Reservation::factory()->create(['attachment_path' => null, 'attachment_name' => null]);

    expect($reservation->hasAttachment())->toBeFalse()
        ->and($reservation->isAttachmentImage())->toBeFalse()
        ->and($reservation->attachment_url)->toBeNull();
});

test('reservation with an image attachment is detected as an image', function () {
    $reservation = Reservation::factory()->create([
        'attachment_path' => 'attachments/foo.jpg',
        'attachment_name' => 'foo.jpg',
    ]);

    expect($reservation->hasAttachment())->toBeTrue()
        ->and($reservation->isAttachmentImage())->toBeTrue()
        ->and($reservation->attachment_url)->toContain('attachments/foo.jpg');
});

test('reservation with a pdf attachment is not detected as an image', function () {
    $reservation = Reservation::factory()->create([
        'attachment_path' => 'attachments/foo.pdf',
        'attachment_name' => 'foo.pdf',
    ]);

    expect($reservation->hasAttachment())->toBeTrue()
        ->and($reservation->isAttachmentImage())->toBeFalse();
});

// ─── Editor: create/edit with attachment ─────────────

test('admin can attach a file while creating a reservation', function () {
    Livewire::test(ReservationEditor::class)
        ->call('openCreate')
        ->set('eventName', 'Reunião com anexo')
        ->set('roomId', $this->room->id)
        ->set('date', now()->addDays(5)->toDateString())
        ->set('startTime', '09:00')
        ->set('endTime', '10:00')
        ->set('requesterName', 'Fulano de Tal')
        ->set('requesterEmail', 'fulano@esdep.mt.gov.br')
        ->set('attendeesCount', 10)
        ->set('attachment', UploadedFile::fake()->create('recibo.jpg', 100, 'image/jpeg'))
        ->call('save')
        ->assertHasNoErrors();

    $reservation = Reservation::where('event_name', 'Reunião com anexo')->firstOrFail();
    expect($reservation->attachment_path)->not->toBeNull()
        ->and($reservation->attachment_name)->toBe('recibo.jpg');
    Storage::disk('public')->assertExists($reservation->attachment_path);
});

test('admin can replace an existing attachment while editing a reservation', function () {
    Storage::disk('public')->put('attachments/old.jpg', 'old-content');
    $reservation = Reservation::factory()->create([
        'room_id' => $this->room->id,
        'attachment_path' => 'attachments/old.jpg',
        'attachment_name' => 'old.jpg',
    ]);

    Livewire::test(ReservationEditor::class)
        ->call('open', $reservation->id)
        ->set('attachment', UploadedFile::fake()->create('novo.jpg', 100, 'image/jpeg'))
        ->call('save')
        ->assertHasNoErrors();

    $reservation->refresh();
    expect($reservation->attachment_name)->toBe('novo.jpg');
    Storage::disk('public')->assertMissing('attachments/old.jpg');
    Storage::disk('public')->assertExists($reservation->attachment_path);
});

test('admin can remove an existing attachment while editing a reservation', function () {
    Storage::disk('public')->put('attachments/old.jpg', 'old-content');
    $reservation = Reservation::factory()->create([
        'room_id' => $this->room->id,
        'attachment_path' => 'attachments/old.jpg',
        'attachment_name' => 'old.jpg',
    ]);

    Livewire::test(ReservationEditor::class)
        ->call('open', $reservation->id)
        ->call('markAttachmentForRemoval')
        ->call('save')
        ->assertHasNoErrors();

    $reservation->refresh();
    expect($reservation->attachment_path)->toBeNull()
        ->and($reservation->attachment_name)->toBeNull();
    Storage::disk('public')->assertMissing('attachments/old.jpg');
});

// ─── Table rendering ─────────────────────────────────

test('reservation manager table shows a thumbnail for an image attachment', function () {
    Storage::disk('public')->put('attachments/foo.jpg', 'content');
    Reservation::factory()->create([
        'room_id' => $this->room->id,
        'attachment_path' => 'attachments/foo.jpg',
        'attachment_name' => 'foo.jpg',
    ]);

    Livewire::test(ReservationManager::class)
        ->assertSee('attachments/foo.jpg', false);
});

test('reservation manager table shows a non-clickable placeholder when there is no attachment', function () {
    Reservation::factory()->create(['room_id' => $this->room->id, 'attachment_path' => null]);

    Livewire::test(ReservationManager::class)
        ->assertSee('Sem anexo', false);
});

// ─── Emails ─────────────────────────────────────────

test('requested, approved and rejected emails embed the image attachment', function () {
    $reservation = Reservation::factory()->create([
        'room_id' => $this->room->id,
        'attachment_path' => 'attachments/foo.jpg',
        'attachment_name' => 'foo.jpg',
    ]);

    expect((new ReservationRequestedMail($reservation))->render())->toContain('attachments/foo.jpg');
    expect((new ReservationApprovedMail($reservation))->render())->toContain('attachments/foo.jpg');
    expect((new ReservationRejectedMail($reservation))->render())->toContain('attachments/foo.jpg');
});

test('approved email links to a pdf attachment instead of embedding it', function () {
    $reservation = Reservation::factory()->create([
        'room_id' => $this->room->id,
        'attachment_path' => 'attachments/foo.pdf',
        'attachment_name' => 'foo.pdf',
    ]);

    $html = (new ReservationApprovedMail($reservation))->render();

    expect($html)->toContain('attachments/foo.pdf')
        ->not->toContain('<img src="'.Storage::disk('public')->url('attachments/foo.pdf'));
});
