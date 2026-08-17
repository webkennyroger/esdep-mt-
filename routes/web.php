<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'site')->name('home');

Route::get('/reservar', \App\Livewire\Public\BookingForm::class)->name('reservations.create');

Route::get('dashboard', \App\Livewire\Admin\Dashboard::class)
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('reservations', \App\Livewire\Admin\ReservationManager::class)->name('reservations.index');
        Route::get('calendar', \App\Livewire\Admin\AdminCalendar::class)->name('calendar');
        Route::get('contacts', \App\Livewire\Admin\ContactManager::class)->name('contacts.index');
    });
});

Route::get('timeline-publico', \App\Livewire\Public\Timeline::class)->name('timeline.public');

require __DIR__.'/auth.php';
