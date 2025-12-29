<?php

declare(strict_types=1);

namespace Gomu\Auth\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Resources\Json\JsonResource;
use Gomu\Auth\Actions\NewUserToken;
use Gomu\Auth\Actions\DeleteCurrentToken;
use Gomu\Auth\Actions\RegisterUser;

final class TokenAuthController
{
    /**
     * @throws ValidationException
     */
    public function store(Request $request, NewUserToken $token): JsonResource
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $newToken = $token->fromRequest($request);

        if (\is_null($newToken)) {
            throw ValidationException::withMessages([
                'email' => [
                    'These credentials do not match our records.',
                ],
            ]);
        }

        return JsonResource::make([
            'access_token' => $newToken->plainTextToken,
        ]);
    }

    public function storeInternal(Request $request, NewUserToken $token): JsonResource
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Check if user is internal
        $user = \Gomu\Auth\Models\User::where('email', $request->email)->first();
        if (!$user || !$user->isInternal()) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials for internal access.'],
            ]);
        }

        $newToken = $token->fromRequest($request);

        if (\is_null($newToken)) {
            throw ValidationException::withMessages([
                'email' => ['These credentials do not match our records.'],
            ]);
        }

        return JsonResource::make([
            'access_token' => $newToken->plainTextToken,
        ]);
    }

    public function storeExternal(Request $request, NewUserToken $token): JsonResource
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Check if user is external
        $user = \Gomu\Auth\Models\User::where('email', $request->email)->first();
        if (!$user || !$user->isExternal()) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials for external access.'],
            ]);
        }

        $newToken = $token->fromRequest($request);

        if (\is_null($newToken)) {
            throw ValidationException::withMessages([
                'email' => ['These credentials do not match our records.'],
            ]);
        }

        return JsonResource::make([
            'access_token' => $newToken->plainTextToken,
        ]);
    }

    public function register(Request $request, RegisterUser $register): JsonResource
    {
        $request->validate([
            'username' => ['nullable', 'string', 'max:50', 'unique:users'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'min:8'],
            'user_type' => ['required', 'in:internal,external'],
            'role_id' => ['nullable', 'uuid', 'exists:roles,id'],
        ]);

        $user = $register->handle($request->only(['username', 'email', 'password', 'user_type', 'role_id']));

        return JsonResource::make([
            'message' => 'User registered successfully',
            'user' => $user,
        ]);
    }

    public function registerInternal(Request $request, RegisterUser $register): JsonResource
    {
        $request->validate([
            'username' => ['nullable', 'string', 'max:50', 'unique:users'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'min:8'],
            'role_id' => ['nullable', 'uuid', 'exists:roles,id'],
        ]);

        $data = $request->only(['username', 'email', 'password', 'role_id']);
        $data['user_type'] = 'internal';

        $user = $register->handle($data);

        return JsonResource::make([
            'message' => 'Internal user registered successfully',
            'user' => $user,
        ]);
    }

    public function registerExternal(Request $request, RegisterUser $register): JsonResource
    {
        $request->validate([
            'username' => ['nullable', 'string', 'max:50', 'unique:users'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'min:8'],
            'role_id' => ['nullable', 'uuid', 'exists:roles,id'],
        ]);

        $data = $request->only(['username', 'email', 'password', 'role_id']);
        $data['user_type'] = 'external';

        $user = $register->handle($data);

        return JsonResource::make([
            'message' => 'External user registered successfully',
            'user' => $user,
        ]);
    }
}