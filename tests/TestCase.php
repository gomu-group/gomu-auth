<?php

declare(strict_types=1);

namespace Gomu\Auth\Tests;

use Orchestra\Testbench;
use Laravel\Sanctum\SanctumServiceProvider;
use Gomu\Auth\GomuAuthServiceProvider;

abstract class TestCase extends Testbench\TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            SanctumServiceProvider::class,
            BerryAuthServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        // Setup environment
    }
}