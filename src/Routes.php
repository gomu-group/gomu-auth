<?php

declare(strict_types=1);

namespace Gomu\Auth;

use Illuminate\Support\Facades\Route;
use Gomu\Auth\Http\Controllers\ProfileController;
use Gomu\Auth\Http\Controllers\TokenAuthController;

final class Routes
{
    public static function authToken(): void
    {
        Route::post('/auth/token', [TokenAuthController::class, 'store']);
        Route::post('/auth/register', [TokenAuthController::class, 'register']);
        Route::delete('/auth/token', [TokenAuthController::class, 'destroy'])->middleware('auth:sanctum');
    }

    public static function internalAuthToken(): void
    {
        Route::post('/auth/internal/token', [TokenAuthController::class, 'storeInternal']);
        Route::post('/auth/internal/register', [TokenAuthController::class, 'registerInternal']);
        Route::delete('/auth/internal/token', [TokenAuthController::class, 'destroy'])->middleware('auth:sanctum');
    }

    public static function externalAuthToken(): void
    {
        Route::post('/auth/external/token', [TokenAuthController::class, 'storeExternal']);
        Route::post('/auth/external/register', [TokenAuthController::class, 'registerExternal']);
        Route::delete('/auth/external/token', [TokenAuthController::class, 'destroy'])->middleware('auth:external');
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

    public static function externalUserProfile(): void
    {
        Route::middleware(['auth:sanctum', 'berry.external'])
            ->get('/external/user-information', [ProfileController::class, 'show']);
    }
}