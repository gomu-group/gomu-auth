<?php

declare(strict_types=1);

namespace Gomu\Auth\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Gomu\Auth\Actions\ListUserToken;
use Laravel\Sanctum\PersonalAccessToken;

final class UserTokenController
{
    public function index(Request $request, ListUserToken $action): JsonResource
    {
        $tokens = $action->fromRequest($request);

        return JsonResource::make([
            'tokens' => $tokens->map(function (PersonalAccessToken $token) {
                return [
                    'id' => $token->id,
                    'name' => $token->name,
                    'abilities' => $token->abilities,
                    'created_at' => $token->created_at,
                    'last_used_at' => $token->last_used_at,
                    'expires_at' => $token->expires_at,
                ];
            }),
        ]);
    }

    public function destroy(Request $request, string $tokenId): JsonResponse
    {
        $token = $request->user()->tokens()->where('id', $tokenId)->first();

        if (!$token) {
            return response()->json([
                'message' => 'Token not found'
            ], 404);
        }

        $token->delete();

        return response()->json([
            'message' => 'Token revoked successfully'
        ]);
    }
}