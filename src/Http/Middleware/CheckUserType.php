<?php

declare(strict_types=1);

namespace Gomu\Auth\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserType
{
    public function handle(Request $request, Closure $next, string $type): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if ($type === 'internal' && !$user->isInternal()) {
            return response()->json(['message' => 'Access denied. Internal access required.'], 403);
        }

        if ($type === 'external' && !$user->isExternal()) {
            return response()->json(['message' => 'Access denied. External access required.'], 403);
        }

        return $next($request);
    }
}