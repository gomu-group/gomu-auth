<?php

declare(strict_types=1);

namespace Gomu\Auth;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class UserAuthServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('gomuauth')
            ->hasConfigFile('gomu-auth')
            ->hasMigrations([
                '2025_12_18_000001_create_roles_table',
                '2025_12_18_000002_create_permissions_table',
                '2025_12_18_000003_create_role_permissions_table',
                '2025_12_18_000004_create_users_table',
                '2025_12_18_000005_create_departments_table',
                '2025_12_18_000006_create_job_levels_table',
                '2025_12_18_000007_create_job_positions_table',
                '2025_12_18_000008_create_employees_table',
                '2025_12_18_000011_create_employee_assignments_table',
            ])
            ->hasRoutes('api');
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(\Laravel\Sanctum\Contracts\HasApiTokens::class, function ($app) {
            return $app->make(\Gomu\Auth\Models\User::class);
        });
    }

    public function packageBooted(): void
    {
        $router = $this->app['router'];
        $router->aliasMiddleware('gomu.internal', \Gomu\Auth\Http\Middleware\CheckUserType::class . ':internal');
        $router->aliasMiddleware('gomu.external', \Gomu\Auth\Http\Middleware\CheckUserType::class . ':external');

        Routes::authToken();
        Routes::userProfile();
        Routes::internalAuthToken();
        Routes::internalUserProfile();
        Routes::externalAuthToken();
        Routes::externalUserProfile();
    }
}