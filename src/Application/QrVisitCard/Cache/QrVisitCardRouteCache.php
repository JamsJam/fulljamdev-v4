<?php

namespace App\Application\QrVisitCard\Cache;

use Psr\Cache\CacheItemInterface;
use Symfony\Contracts\Cache\CacheInterface;

final readonly class QrVisitCardRouteCache
{
    private const KEY = 'cache.qr_visit_card.route';

    public function __construct(private CacheInterface $cache)
    {
    }

    /** @param callable(): string $loader */
    public function get(callable $loader): string
    {
        return $this->cache->get(self::KEY, static function (CacheItemInterface $item) use ($loader): string {
            $item->expiresAfter(null);

            return $loader();
        });
    }

    public function invalidate(): void
    {
        $this->cache->delete(self::KEY);
    }
}
