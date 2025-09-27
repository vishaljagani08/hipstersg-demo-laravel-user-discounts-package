<?php

namespace HipstersgDemo\LaravelUserDiscountsPackage;

use HipstersgDemo\LaravelUserDiscountsPackage\Services\DiscountManager;
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
        
        $this->publishes([
            __DIR__ . '/../database/seeders' => database_path('seeders'),
        ], 'seeders');


        // Merge default config
        $this->mergeConfigFrom(__DIR__ . '/../config/discounts.php', 'discounts');
    }


    public function register()
    {
        $this->app->singleton('discounts', function ($app) {
            return new DiscountManager($app['db']->connection()); 
        });

        // Alias for container resolution
        $this->app->alias('discounts', Services\DiscountManager::class);
        $this->app->alias('discounts', Facades\Discounts::class);
    }
}
