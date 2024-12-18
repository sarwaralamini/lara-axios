<?php

namespace Sarwar\PopupFileManager;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class FileManagerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        //dd(Route::getRoutes());
        $this->loadViewsFrom(__DIR__ . '/resources/views/components', 'Sarwar\PopupFileManager');
        //dd(View::getFinder()->getPaths());
        // $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        $this->publishes([
            __DIR__ . '/config/pupup-file-manager.php' => config_path('pupup-file-manager.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/resources/js/popup-file-manager.js' => public_path('dist/js/popup-file-manager.js'),
            __DIR__ . '/resources/css/popup-file-manager.css' => public_path('dist/css/popup-file-manager.css'),
            __DIR__ . '/resources/views/vendor' => resource_path('views/vendor'),
        ], 'public');

        $this->publishes([
            __DIR__ . '/resources/views/vendor' => resource_path('views/vendor'),
        ], 'views');

        // Registers a single Blade component with the alias 'file-manager-modal',
        //Blade::component('file-manager-modal', 'Sarwar\PopupFileManager::file-manager-modal');

        // Registers multiple Blade components using an array of aliases.
        Blade::components([
            'file-manager-modal' => 'Sarwar\PopupFileManager::file-manager-modal',
        ]);
    }
}
