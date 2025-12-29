<?php

declare(strict_types=1);

namespace Gomu\Auth\Actions;

use DateTimeInterface;
use Illuminate\Support\Arr;
use Jenssegers\Agent\Agent;
use Illuminate\Http\Request;
use Laravel\Sanctum\NewAccessToken;
use Illuminate\Contracts\Auth\Factory;
use Berry\Auth\Models\User;

final class NewUserToken
{
    public function __construct(
        private readonly Factory $auth
    ) {}

    /**
     * @param  array<string, string>  $credentials
     * @param  array<string>  $abilities
     */
    public function handle(array $credentials, string $tokenName, array $abilities = ['*'], ?DateTimeInterface $expiresAt = null): ?NewAccessToken
    {
        $guard = $this->auth->guard();

        if (\config('berry-auth.hashing_password_before_attempt', true)) {
            $plainPassword = Arr::get($credentials, 'password');

            Arr::set($credentials, 'password', \md5($plainPassword));
        }

        $guard->attempt($credentials, true);

        /** @var mixed $user */
        $user = $guard->user();

        return $user?->createToken($tokenName, $abilities, $expiresAt);
    }

    public function fromRequest(Request $request): ?NewAccessToken
    {
        $agent = new Agent($request->headers->all(), $request->userAgent());

        /** @var string $device */
        $device = $agent->device();

        /** @var string $platform */
        $platform = $agent->platform();

        $tokenName = \sprintf('%s on %s', $device, $platform);

        return $this->handle($request->only(['email', 'password']), $tokenName);
    }
}