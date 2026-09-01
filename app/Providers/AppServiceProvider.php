<?php

namespace App\Providers;

use App\Services\FcmService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // FcmService sebagai singleton agar access token di-cache dalam satu request cycle
        $this->app->singleton(FcmService::class);
    }

    public function boot(): void
    {
        //
    }
}
