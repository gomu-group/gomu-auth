<?php

declare(strict_types=1);

namespace Gomu\Auth\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Gomu\Auth\Models\User;
use Gomu\Auth\Http\Resources\UserResource;

final class UserController
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = User::query();

        // Filter by user_type if provided
        if ($request->has('user_type')) {
            $query->where('user_type', $request->user_type);
        }

        // Search by email or username
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate($request->get('per_page', 15));

        return UserResource::collection($users);
    }

    public function store(Request $request): JsonResource
    {
        $validated = $request->validate([
            'username' => ['nullable', 'string', 'max:50', 'unique:users'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'min:8'],
            'user_type' => ['required', 'in:internal,external'],
            'role_id' => ['nullable', 'uuid', 'exists:roles,id'],
        ]);

        $user = User::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password_hash' => bcrypt($validated['password']),
            'user_type' => $validated['user_type'],
            'role_id' => $validated['role_id'] ?? null,
        ]);

        return UserResource::make($user);
    }

    public function show($userId): JsonResource
    {
        $user = User::findOrFail($userId);
        return UserResource::make($user->load('employee'));
    }

    public function update(Request $request, $userId): JsonResource
    {
        $user = User::findOrFail($userId);

        $validated = $request->validate([
            'username' => ['nullable', 'string', 'max:50', 'unique:users,username,' . $user->id],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'min:8'],
            'user_type' => ['required', 'in:internal,external'],
            'role_id' => ['nullable', 'uuid', 'exists:roles,id'],
        ]);

        $updateData = [
            'username' => $validated['username'],
            'email' => $validated['email'],
            'user_type' => $validated['user_type'],
            'role_id' => $validated['role_id'] ?? null,
        ];

        if (!empty($validated['password'])) {
            $updateData['password_hash'] = bcrypt($validated['password']);
        }

        $user->update($updateData);

        return UserResource::make($user->fresh());
    }

    public function destroy($userId): JsonResponse
    {
        $user = User::findOrFail($userId);

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}