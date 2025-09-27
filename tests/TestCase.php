<?php

namespace HipstersgDemo\LaravelUserDiscountsPackage\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase as Orchestra;
use HipstersgDemo\LaravelUserDiscountsPackage\DiscountServiceProvider;

abstract class TestCase extends Orchestra
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // run package migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // also load app's default migrations (users table, etc.)
        $this->loadLaravelMigrations();
    }

    /**
     * Tell Testbench which service providers to register.
     */
    protected function getPackageProviders($app)
    {
        return [
            DiscountServiceProvider::class,
        ];
    }

    /**
     * If your package needs config overrides for testing.
     */
    protected function getEnvironmentSetUp($app)
    {
        // Example: use in-memory SQLite for faster tests
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}
