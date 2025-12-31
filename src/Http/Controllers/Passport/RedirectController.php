<?php

declare(strict_types=1);

namespace Gomu\Auth\Http\Controllers\Passport;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Gomu\Auth\Passport;

final class RedirectController
{
    public function create(Request $request): RedirectResponse
    {
        return Passport::redirect($request);
    }
}