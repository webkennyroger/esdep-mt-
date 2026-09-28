<?php

use App\Livewire\Admin\ReservationManager;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use App\Services\ReservationExportService;
use Livewire\Livewire;
use PhpOffice\PhpSpreadsheet\IOFactory;

beforeEach(function () {
    $this->actingAs(User::factory()->superAdmin()->create());
    $this->room = Room::create([
        'name' => 'Sala Horizonte',
        'code' => 'HORIZONTE',
        'capacity' => 60,
        'theme_color' => '#008b5c',
        'is_active' => true,
    ]);
});

// ─── Service ─────────────────────────────────────

test('export service generates a valid xlsx file with the reservation data', function () {
    Reservation::factory()->create(['room_id' => $this->room->id, 'event_name' => 'Reunião Exportada']);
    $reservations = Reservation::with('room')->get();

    $bytes = app(ReservationExportService::class)->toXlsx($reservations);

    expect(substr($bytes, 0, 2))->toBe('PK');

    $tmp = tempnam(sys_get_temp_dir(), 'xlsx');
    file_put_contents($tmp, $bytes);
    $sheet = IOFactory::load($tmp)->getActiveSheet();
    unlink($tmp);

    expect($sheet->getCell('A1')->getValue())->toBe('Protocolo')
        ->and($sheet->getCell('B2')->getValue())->toBe('Reunião Exportada');
});

test('export service generates a valid pdf file', function () {
    Reservation::factory()->create(['room_id' => $this->room->id, 'event_name' => 'Reunião em PDF']);
    $reservations = Reservation::with('room')->get();

    $bytes = app(ReservationExportService::class)->toPdf($reservations);

    expect(substr($bytes, 0, 4))->toBe('%PDF');
});

// ─── ReservationManager actions ──────────────────

test('copy text includes all filtered reservations when none are selected', function () {
    Reservation::factory()->create(['room_id' => $this->room->id, 'event_name' => 'Evento Um']);
    Reservation::factory()->create(['room_id' => $this->room->id, 'event_name' => 'Evento Dois']);

    $text = Livewire::test(ReservationManager::class)->call('copyText')->effects['returns'][0];

    expect($text)->toContain('Evento Um')
        ->toContain('Evento Dois');
});

test('copy text includes only the selected reservations when some are selected', function () {
    $one = Reservation::factory()->create(['room_id' => $this->room->id, 'event_name' => 'Evento Um']);
    Reservation::factory()->create(['room_id' => $this->room->id, 'event_name' => 'Evento Dois']);

    $text = Livewire::test(ReservationManager::class)
        ->set('selected', [$one->id])
        ->call('copyText')
        ->effects['returns'][0];

    expect($text)->toContain('Evento Um')
        ->not->toContain('Evento Dois');
});

test('export excel action returns a downloadable xlsx response', function () {
    Reservation::factory()->create(['room_id' => $this->room->id, 'event_name' => 'Evento Excel']);

    $response = app(ReservationManager::class)->exportExcel();

    expect($response->headers->get('Content-Type'))->toContain('spreadsheet');
});

test('export pdf action returns a downloadable pdf response', function () {
    Reservation::factory()->create(['room_id' => $this->room->id, 'event_name' => 'Evento PDF']);

    $response = app(ReservationManager::class)->exportPdf();

    expect($response->headers->get('Content-Type'))->toContain('application/pdf');
});

test('print html includes the filtered reservations', function () {
    Reservation::factory()->create(['room_id' => $this->room->id, 'event_name' => 'Evento Impresso']);

    $html = Livewire::test(ReservationManager::class)->call('printHtml')->effects['returns'][0];

    expect($html)->toContain('Evento Impresso');
});
