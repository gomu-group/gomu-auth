<?php

declare(strict_types=1);

namespace Gomu\Auth\Passport;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Gomu\Auth\Passport;

final class Redirector
{
    public static function make(): self
    {
        return new self;
    }

    public function redirect(Request $request): RedirectResponse
    {
        $query = http_build_query([
            'client_id' => Passport::clientId(),
            'redirect_uri' => Passport::callbackUrl(),
            'response_type' => 'code',
            'scope' => '',
            'state' => $this->generateState($request),
        ]);

        return redirect(Passport::redirectUrl($query));
    }

    private function generateState(Request $request): string
    {
        try {
            $state = $request->session()->get('_passport_state') ?? str()->random(40);
            $request->session()->put('_passport_state', $state);
            return $state;
        } catch (\RuntimeException $e) {
            // Session not available (e.g., in testing), generate random state
            return str()->random(40);
        }
    }
}