<?php

namespace SHTayeb\Bookworm;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
class BookwormServiceProvider extends ServiceProvider
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
        Route::prefix('bookworm')
            ->as('bookworm.')
            ->group(function () {
                $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
            });

        if ($this->app->runningInConsole()) {
            // In addition to publishing assets, we also publish the config
            $this->publishes([
                __DIR__.'/../config/bookworm.php' => config_path('bookworm.php'),
            ], 'shtayeb-bookworm-config');
        }
    }
}
