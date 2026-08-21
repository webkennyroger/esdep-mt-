<?php

use App\Http\Controllers\Auth\ContactLoginController;
use App\Livewire\Admin\ActivityLogController;
use App\Livewire\Admin\AdminCalendar;
use App\Livewire\Admin\ContactManager;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Reports;
use App\Livewire\Admin\ReservationManager;
use App\Livewire\Admin\RoomManager;
use App\Livewire\Admin\UserProfile;
use App\Livewire\Contact\ContactActivity;
use App\Livewire\Contact\ContactBooking;
use App\Livewire\Contact\ContactCalendar;
use App\Livewire\Contact\ContactDashboard;
use App\Livewire\Contact\ContactGroups;
use App\Livewire\Contact\ContactProfile;
use App\Livewire\Contact\ContactReports;
use App\Livewire\Contact\ContactReservationEdit;
use App\Livewire\Contact\ContactReservations;
use App\Livewire\Contact\ContactRooms;
use App\Livewire\Public\BookingForm;
use App\Livewire\Public\ReservationStatusCheck;
use App\Livewire\Public\Timeline;
use Illuminate\Support\Facades\Route;

Route::view('/', 'site')->name('home');

Route::get('/reservar', BookingForm::class)
    ->middleware(['auth'])
    ->name('reservations.create');
Route::get('/consultar', ReservationStatusCheck::class)->name('reservations.check');

Route::get('dashboard', Dashboard::class)
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('reservations', ReservationManager::class)->name('reservations.index');
        Route::get('calendar', AdminCalendar::class)->name('calendar');
        Route::get('rooms', RoomManager::class)->name('rooms.index');
        Route::get('contacts', ContactManager::class)->name('contacts.index');
        Route::get('activity', ActivityLogController::class)->name('activity');
        Route::get('reports', Reports::class)->name('reports');
        Route::get('profile', UserProfile::class)->name('profile');
    });
});

Route::get('timeline-publico', Timeline::class)->name('timeline.public');

// Contact Auth
Route::middleware('guest:contact')->group(function () {
    Route::get('contato/login', [ContactLoginController::class, 'showLoginForm'])->name('contact.login');
    Route::post('contato/login', [ContactLoginController::class, 'login'])
        ->middleware('throttle:5,1')->name('contact.login.submit');
});

Route::middleware('auth:contact')->group(function () {
    Route::post('contato/logout', [ContactLoginController::class, 'logout'])->name('contact.logout');
    Route::get('contato/painel', ContactDashboard::class)->name('contact.dashboard');
    Route::get('contato/salas', ContactRooms::class)->name('contact.rooms');
    Route::get('contato/agenda', ContactCalendar::class)->name('contact.calendar');
    Route::get('contato/reservar', ContactBooking::class)->name('contact.booking');
    Route::get('contato/reservas', ContactReservations::class)->name('contact.reservations');
    Route::get('contato/grupos', ContactGroups::class)->name('contact.groups');
    Route::get('contato/relatorios', ContactReports::class)->name('contact.reports');
    Route::get('contato/atividades', ContactActivity::class)->name('contact.activity');
    Route::get('contato/perfil', ContactProfile::class)->name('contact.profile');
    Route::get('contato/reserva/{reservation}/editar', ContactReservationEdit::class)->name('contact.reservation.edit');
});

require __DIR__.'/auth.php';
