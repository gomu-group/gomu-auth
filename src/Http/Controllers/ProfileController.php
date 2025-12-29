<?php

declare(strict_types=1);

namespace Gomu\Auth\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Gomu\Auth\Models\User;

final class ProfileController
{
    public function show(Request $request): JsonResource
    {
        /** @var User $user */
        $user = $request->user();

        return JsonResource::make([
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'role' => $user->role,
            'employee' => $user->employee,
        ]);
    }
}