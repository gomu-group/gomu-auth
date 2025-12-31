<?php

declare(strict_types=1);

namespace Gomu\Auth;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

final class SanctumStateful
{
    private static array $ports = [
        '3000', '3001', '3002', '3003', '3005',
    ];

    public static function domains(array|string $subdomains): array
    {
        $subdomainWithPorts = Arr::flatten(array_map(static function (string $domain): array|string {
            return Str::contains($domain, ':') ? $domain : array_map(static fn (string $port) => sprintf('%s:%s', $domain, $port), self::$ports);
        }, Arr::wrap($subdomains)));

        return array_unique(
            array_merge($subdomains, $subdomainWithPorts)
        );
    }
}