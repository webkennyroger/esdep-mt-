<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'site')->name('home');

Route::get('/reservar', \App\Livewire\Public\BookingForm::class)
    ->middleware(['auth'])
    ->name('reservations.create');
Route::get('/consultar', \App\Livewire\Public\ReservationStatusCheck::class)->name('reservations.check');

Route::get('dashboard', \App\Livewire\Admin\Dashboard::class)
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('reservations', \App\Livewire\Admin\ReservationManager::class)->name('reservations.index');
        Route::get('calendar', \App\Livewire\Admin\AdminCalendar::class)->name('calendar');
        Route::get('rooms', \App\Livewire\Admin\RoomManager::class)->name('rooms.index');
        Route::get('contacts', \App\Livewire\Admin\ContactManager::class)->name('contacts.index');
        Route::get('activity', \App\Livewire\Admin\ActivityLogController::class)->name('activity');
        Route::get('reports', \App\Livewire\Admin\Reports::class)->name('reports');
        Route::get('profile', \App\Livewire\Admin\UserProfile::class)->name('profile');
    });
});

Route::get('timeline-publico', \App\Livewire\Public\Timeline::class)->name('timeline.public');

// Contact Auth
Route::middleware('guest:contact')->group(function () {
    Route::get('contato/login', [\App\Http\Controllers\Auth\ContactLoginController::class, 'showLoginForm'])->name('contact.login');
    Route::post('contato/login', [\App\Http\Controllers\Auth\ContactLoginController::class, 'login'])->name('contact.login.submit');
});

Route::middleware('auth:contact')->group(function () {
    Route::post('contato/logout', [\App\Http\Controllers\Auth\ContactLoginController::class, 'logout'])->name('contact.logout');
    Route::get('contato/painel', \App\Livewire\Contact\ContactDashboard::class)->name('contact.dashboard');
    Route::get('contato/reserva/{reservation}/editar', \App\Livewire\Contact\ContactReservationEdit::class)->name('contact.reservation.edit');
});

require __DIR__.'/auth.php';
