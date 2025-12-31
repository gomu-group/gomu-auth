<?php

declare(strict_types=1);

namespace Gomu\Auth\Http\Controllers\Passport;

use Throwable;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Gomu\Auth\Passport;
use Gomu\Auth\Passport\UserProfile;
use Gomu\Auth\Models\User;

final class CallbackController
{
    /**
     * @throws Throwable
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        try {
            $profile = Passport::resolve($request);

            $user = $this->findOrCreateUser($profile);

            // Create token for the user
            $token = $user->createToken('OAuth Login');

            // Return JSON response for API or redirect for web
            if ($request->expectsJson()) {
                return response()->json([
                    'access_token' => $token->plainTextToken,
                    'token_type' => 'Bearer',
                    'user' => $user,
                ]);
            }

            // For web requests, redirect with token in session or similar
            return redirect('/dashboard')->with('token', $token->plainTextToken);

        } catch (Throwable $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Authentication failed',
                    'message' => $e->getMessage()
                ], 401);
            }

            return redirect('/login')->withErrors(['oauth' => 'Authentication failed']);
        }
    }

    private function findOrCreateUser(UserProfile $profile): User
    {
        $user = User::where('email', $profile->email)->first();

        if (!$user) {
            $user = User::create([
                'username' => $profile->email, // or generate unique username
                'email' => $profile->email,
                'password_hash' => '', // OAuth users don't need password
                'user_type' => 'external',
            ]);
        }

        return $user;
    }
}