<?php

declare(strict_types=1);

namespace Gomu\Auth\Actions;

use Gomu\Auth\Models\User;
use Illuminate\Support\Str;

final class RegisterUser
{
    public function handle(array $data): User
    {
        $user = new User([
            'id' => (string) Str::uuid(),
            'username' => $data['username'] ?? null,
            'email' => $data['email'],
            'password_hash' => bcrypt($data['password']),
            'user_type' => $data['user_type'] ?? 'external',
            'role_id' => $data['role_id'] ?? null,
            'force_password_change' => $data['force_password_change'] ?? true,
        ]);

        $user->save();

        return $user;
    }
}