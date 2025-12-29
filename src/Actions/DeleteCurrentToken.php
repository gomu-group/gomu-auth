<?php

declare(strict_types=1);

namespace Gomu\Auth\Actions;

use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

final class DeleteCurrentToken
{
    public function handle(PersonalAccessToken $token): bool
    {
        return $token->delete();
    }

    public function fromRequest(Request $request): bool
    {
        /** @var PersonalAccessToken $token */
        $token = $request->user()->currentAccessToken();

        return $this->handle($token);
    }
}