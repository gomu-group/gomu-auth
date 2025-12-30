<?php

declare(strict_types=1);

namespace Gomu\Auth\Tests;

use Orchestra\Testbench;
use Laravel\Sanctum\SanctumServiceProvider;
use Gomu\Auth\UserAuthServiceProvider;

abstract class TestCase extends Testbench\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Load migrations for testing
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadMigrationsFrom(__DIR__ . '/../vendor/laravel/sanctum/database/migrations');
    }
    protected function getPackageProviders($app): array
    {
        return [
            SanctumServiceProvider::class,
            UserAuthServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // Load and set config
        $app['config']->set('gomu-auth', array_merge(require __DIR__.'/../config/gomu-auth.php', ['schema' => '', 'hashing_password_before_attempt' => false]));

        // Set auth connection to testing
        $app['config']->set('gomu-auth.database_connection', 'testing');

        // Set auth config
        $app['config']->set('auth', [
            'defaults' => [
                'guard' => 'web',
                'passwords' => 'users',
            ],
            'guards' => [
                'web' => [
                    'driver' => 'session',
                    'provider' => 'users',
                ],
                'sanctum' => [
                    'driver' => 'sanctum',
                    'provider' => 'users',
                ],
            ],
            'providers' => [
                'users' => [
                    'driver' => 'eloquent',
                    'model' => \Gomu\Auth\Models\User::class,
                ],
            ],
        ]);
    }
}