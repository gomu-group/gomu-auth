<?php

declare(strict_types=1);

namespace Gomu\Auth\Actions;

use DateTimeInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Jenssegers\Agent\Agent;
use Illuminate\Http\Request;
use Laravel\Sanctum\NewAccessToken;
use Illuminate\Contracts\Auth\Factory;
use Gomu\Auth\Models\User;

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
        // Custom authentication without guard to avoid schema issues
        $user = \Gomu\Auth\Models\User::where('email', $credentials['email'])->first();
        
        if (!$user) {
            Log::error('User not found', ['email' => $credentials['email']]);
            return null;
        }

        $password = $credentials['password'];
        
        // Check if hashing is enabled (hardcoded to false for testing)
        if (false) { // \config('gomu-auth.hashing_password_before_attempt', true)) {
            $password = \md5($password);
            Log::error('Using MD5 hash', ['original' => $credentials['password'], 'hashed' => $password]);
        } else {
            Log::error('Using plain password', ['password' => $password]);
        }

        // Check password
        $isValid = Hash::check($password, $user->password_hash);
        Log::error('Password check', [
            'is_valid' => $isValid,
            'stored_hash' => $user->password_hash,
            'provided_password' => $password
        ]);

        if (!$isValid) {
            return null;
        }

        return $user->createToken($tokenName, $abilities, $expiresAt);
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