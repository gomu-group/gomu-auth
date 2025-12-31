<?php

declare(strict_types=1);

namespace Gomu\Auth;

use Illuminate\Support\Facades\Route;
use Gomu\Auth\Http\Controllers\ProfileController;
use Gomu\Auth\Http\Controllers\TokenAuthController;
use Gomu\Auth\Http\Controllers\UserController;
use Gomu\Auth\Http\Controllers\EmployeeController;

final class Routes
{
    public static function authToken(): void
    {
        Route::post('/token', [TokenAuthController::class, 'store']);
        Route::post('/register', [TokenAuthController::class, 'register']);
        Route::delete('/token', [TokenAuthController::class, 'destroy'])->middleware('auth:sanctum');
    }

    public static function internalAuthToken(): void
    {
        Route::post('/internal/token', [TokenAuthController::class, 'storeInternal']);
        Route::post('/internal/register', [TokenAuthController::class, 'registerInternal']);
        Route::delete('/internal/token', [TokenAuthController::class, 'destroy'])->middleware('auth:sanctum');
    }

    public static function externalAuthToken(): void
    {
        Route::post('/external/token', [TokenAuthController::class, 'storeExternal']);
        Route::post('/external/register', [TokenAuthController::class, 'registerExternal']);
        Route::delete('/external/token', [TokenAuthController::class, 'destroy'])->middleware('auth:sanctum');
    }

    public static function userProfile(): void
    {
        Route::middleware('auth:sanctum')
            ->get('/user-information', [ProfileController::class, 'show']);
    }

    public static function internalUserProfile(): void
    {
        Route::middleware(['auth:sanctum', 'gomu.internal'])
            ->get('/internal/user-information', [ProfileController::class, 'show']);
    }

    public static function userManagement(): void
    {
        Route::apiResource('users', UserController::class);
    }

    public static function employeeManagement(): void
    {
        Route::apiResource('employees', EmployeeController::class);
    }

    public static function userToken(): void
    {
        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/user-token', [\Gomu\Auth\Http\Controllers\UserTokenController::class, 'index']);
            Route::delete('/user-token/{tokenId}', [\Gomu\Auth\Http\Controllers\UserTokenController::class, 'destroy']);
        });
    }

    public static function passport(): void
    {
        Route::get('/oauth/passport/redirect', [\Gomu\Auth\Http\Controllers\Passport\RedirectController::class, 'create']);
        Route::get('/oauth/passport/callback', [\Gomu\Auth\Http\Controllers\Passport\CallbackController::class, 'store']);
    }
}