<?php

namespace HipstersgDemo\LaravelUserDiscountsPackage;

use Illuminate\Support\ServiceProvider;

class DiscountServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Publish config
        $this->publishes([
            __DIR__ . '/../config/discounts.php' => config_path('discounts.php'),
        ], 'config');


        // Load migrations from package's database/migrations directory
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');


        // Merge default config
        $this->mergeConfigFrom(__DIR__ . '/../config/discounts.php', 'discounts');
    }


    public function register()
    {
        $this->app->singleton('discounts', function ($app) {
            return new Services\DiscountManager($app->make('db'));
        });

        // Alias for container resolution
        $this->app->alias('discounts', Services\DiscountManager::class);
        $this->app->alias('discounts', Facades\Discounts::class);
    }
}
