<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

Route::get('/__diag', function () {
    $info = [];
    $info['php_version'] = PHP_VERSION;
    $info['laravel_version'] = app()->version();
    $info['app_env'] = env('APP_ENV', 'NOT SET');
    $info['app_debug'] = env('APP_DEBUG', 'NOT SET') ? 'true' : 'false';
    $info['app_key_set'] = env('APP_KEY') ? 'YES' : 'NO';
    $info['db_connection'] = env('DB_CONNECTION', 'NOT SET');
    $info['session_driver'] = env('SESSION_DRIVER', 'NOT SET');
    $info['cache_store'] = env('CACHE_STORE', 'NOT SET');
    $info['queue_connection'] = env('QUEUE_CONNECTION', 'NOT SET');
    $info['base_path'] = base_path();
    $info['storage_writable'] = is_writable(storage_path()) ? 'YES' : 'NO';
    $info['env_exists'] = file_exists(base_path('.env')) ? 'YES' : 'NO';
    $info['env_production_exists'] = file_exists(base_path('.env.production')) ? 'YES' : 'NO';
    $info['database_sqlite_exists'] = file_exists(database_path('database.sqlite')) ? 'YES' : 'NO';

    try {
        \Illuminate\Support\Facades\DB::connection()->getPdo();
        $info['db_connected'] = 'YES';
        $info['db_server_version'] = \Illuminate\Support\Facades\DB::connection()->getServerVersion();
    } catch (\Exception $e) {
        $info['db_connected'] = 'NO: ' . $e->getMessage();
    }

    header('Content-Type: text/plain');
    foreach ($info as $key => $value) {
        echo $key . ': ' . $value . "\n";
    }
    exit;
});

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
