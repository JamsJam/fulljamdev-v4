<?php

namespace App\Application\Settings\General\Cache;

use App\Application\Settings\General\Dto\GeneralSettingsDto;
use Psr\Cache\CacheItemInterface;
use Symfony\Contracts\Cache\CacheInterface;

final readonly class GeneralSettingsCache
{
    public const KEY = 'cache.setting.GeneralSettingsDto';

    public function __construct(private CacheInterface $cache)
    {
    }

    /** @param callable(): GeneralSettingsDto $loader */
    public function get(callable $loader): GeneralSettingsDto
    {
        $settings = $this->cache->get(self::KEY, static function (CacheItemInterface $item) use ($loader): GeneralSettingsDto {
            $item->expiresAfter(null);

            return $loader();
        });

        return clone $settings;
    }

    public function invalidate(): void
    {
        $this->cache->delete(self::KEY);
    }
}
