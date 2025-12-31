<?php

declare(strict_types=1);

namespace Gomu\Auth;

use Throwable;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Gomu\Auth\Passport\Redirector;
use Gomu\Auth\Passport\UserProfile;
use Gomu\Auth\Passport\UserResolver;

final class Passport
{
    public static function baseUrl(): string
    {
        /** @var string */
        return config('services.passport.base_url');
    }

    public static function logout(): string
    {
        return self::baseUrl().'/logout';
    }

    public static function redirectUrl(string $query): string
    {
        return self::baseUrl().'/oauth/authorize?'.$query;
    }

    public static function profileUrl(): string
    {
        return self::baseUrl().'/api/oauth/user-profile';
    }

    public static function clientId(): string
    {
        /** @var string */
        return config('services.passport.client_id');
    }

    public static function callbackUrl(): string
    {
        /** @var string */
        return config('services.passport.callback_url');
    }

    public static function redirect(Request $request): RedirectResponse
    {
        return Redirector::make()->redirect($request);
    }

    /**
     * @throws Throwable
     */
    public static function resolve(Request $request): UserProfile
    {
        return UserResolver::make()->resolve($request);
    }
}