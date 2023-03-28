<?php

namespace SHTayeb\Bookworm;

use Illuminate\Support\ServiceProvider;
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

        if ($this->app->runningInConsole()) {
            //  publish the config
            $this->publishes([
                __DIR__.'/../config/bookworm.php' => config_path('bookworm.php'),
            ], 'shtayeb-bookworm-config');
        }
    }
}
