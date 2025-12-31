<?php

declare(strict_types=1);

namespace Gomu\Auth\Passport;

use Throwable;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Illuminate\Support\Facades\Http;
use Gomu\Auth\Passport;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\ConnectionException;

final class UserResolver
{
    public static function make(): self
    {
        return new self;
    }

    /**
     * @throws Throwable
     */
    public function resolve(Request $request): UserProfile
    {
        $accessToken = $this->createAccessToken($request);

        return $this->fetchPassportUser($accessToken);
    }

    /**
     * @throws ConnectionException
     */
    private function fetchPassportUser(string $token): UserProfile
    {
        $response = $this->getHttpClient()->asJson()->withToken($token)->get(Passport::profileUrl());

        if ($response->failed()) {
            throw new InvalidArgumentException('Unable to fetch user profile from Passport server');
        }

        return new UserProfile(
            $response->json('id'),
            $response->json('name'),
            $response->json('email'),
            $response->json('loginable_id'),
            $response->json('loginable_type'),
        );
    }

    /**
     * @throws Throwable
     */
    private function createAccessToken(Request $request): string
    {
        $code = $request->get('code');

        if (!$code) {
            throw new InvalidArgumentException('Authorization code is required');
        }

        $response = $this->getHttpClient()->asForm()->post(Passport::baseUrl().'/oauth/token', [
            'grant_type' => 'authorization_code',
            'client_id' => Passport::clientId(),
            'client_secret' => config('services.passport.client_secret'),
            'redirect_uri' => Passport::callbackUrl(),
            'code' => $code,
        ]);

        if ($response->failed()) {
            throw new InvalidArgumentException('Unable to exchange authorization code for access token');
        }

        return $response->json('access_token');
    }

    private function getHttpClient(): PendingRequest
    {
        return Http::timeout(30)->retry(3, 100);
    }
}