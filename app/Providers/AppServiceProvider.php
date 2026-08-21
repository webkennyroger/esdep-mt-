<?php

namespace App\Providers;

use App\Services\ConflictCheckerService;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ConflictCheckerService::class);
    }

    public function boot(): void
    {
        Carbon::setLocale('pt_BR');
        setlocale(LC_TIME, 'pt_BR.UTF-8');

        // Set application locale for pagination translations
        app()->setLocale('pt_BR');
    }
}
