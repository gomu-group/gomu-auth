<?php

declare(strict_types=1);

namespace Gomu\Auth\Actions;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

final class ListUserToken
{
    /**
     * @return Collection<int, PersonalAccessToken>
     */
    public function handle(Request $request): Collection
    {
        return $request->user()->tokens()->get();
    }

    /**
     * @return Collection<int, PersonalAccessToken>
     */
    public function fromRequest(Request $request): Collection
    {
        return $this->handle($request);
    }
}