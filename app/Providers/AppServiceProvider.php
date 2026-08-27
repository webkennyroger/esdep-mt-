<?php

namespace App\Providers;

use App\Services\ConflictCheckerService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use SocialiteProviders\Keycloak\KeycloakExtendSocialite;
use SocialiteProviders\Manager\SocialiteWasCalled;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ConflictCheckerService::class);
    }

    public function boot(): void
    {
        Event::listen(SocialiteWasCalled::class, [KeycloakExtendSocialite::class, 'handle']);

        Carbon::setLocale('pt_BR');
        setlocale(LC_TIME, 'pt_BR.UTF-8');

        // Set application locale for pagination translations
        app()->setLocale('pt_BR');
    }
}
