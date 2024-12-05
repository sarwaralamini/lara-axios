<?php

namespace App\Providers;
use Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Route::aliasMiddleware('web.auth', \App\Http\Middleware\Authenticate::class);
        Route::aliasMiddleware('redirect.auth', \App\Http\Middleware\RedirectIfAuthenticated::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(125);
    }
}
