<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Authentication Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the BerryAuth package.
    |
    */

    'database_connection' => env('AUTH_DB_CONNECTION', 'pgsql'),

    'hashing_password_before_attempt' => env('BERRY_AUTH_HASH_PASSWORD', true),

    'models' => [
        'user' => \Gomu\Auth\Models\User::class,
        'employee' => \Gomu\Auth\Models\Employee::class,
        'department' => \Gomu\Auth\Models\Department::class,
        'role' => \Gomu\Auth\Models\Role::class,
        'permission' => \Gomu\Auth\Models\Permission::class,
        'job_level' => \Gomu\Auth\Models\JobLevel::class,
        'job_position' => \Gomu\Auth\Models\JobPosition::class,
        'employee_assignment' => \Gomu\Auth\Models\EmployeeAssignment::class,
    ],

    'guards' => [
        'api' => [
            'driver' => 'sanctum',
            'provider' => 'users',
        ],
        'internal' => [
            'driver' => 'sanctum',
            'provider' => 'internal_users',
        ],
        'external' => [
            'driver' => 'sanctum',
            'provider' => 'external_users',
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => \Gomu\Auth\Models\User::class,
        ],
        'internal_users' => [
            'driver' => 'eloquent',
            'model' => \Gomu\Auth\Models\User::class,
        ],
        'external_users' => [
            'driver' => 'eloquent',
            'model' => \Gomu\Auth\Models\User::class,
        ],
    ],
];