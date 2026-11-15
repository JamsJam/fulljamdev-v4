<?php

namespace App\Application\QrVisitCard\Proxy;

use App\Application\QrVisitCard\Cache\QrVisitCardRouteCache;

final readonly class QrVisitCardRouteProxy
{
    public function __construct(private QrVisitCardRouteCache $cache)
    {
    }

    /** @param callable(): string $loader */
    public function get(callable $loader): string
    {
        return $this->cache->get($loader);
    }

    public function invalidate(): void
    {
        $this->cache->invalidate();
    }
}
