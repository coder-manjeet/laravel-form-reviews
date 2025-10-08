<?php

namespace CoderManjeet\LaravelFormReviews\Providers;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;


class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        $this->publishes([
            __DIR__ . '/../../config/form-reviews.php' => config_path('form-reviews.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/../../resources/views' => resource_path('views/vendor/form-reviews'),
        ], 'views');

        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'form-reviews');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/form-reviews.php', 'form-reviews');
    }
}