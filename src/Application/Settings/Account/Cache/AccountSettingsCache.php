<?php

namespace App\Application\Settings\Account\Cache;

use App\Application\Settings\Account\Dto\AccountSettingsDto;
use Psr\Cache\CacheItemInterface;
use Symfony\Contracts\Cache\CacheInterface;

final readonly class AccountSettingsCache
{
    public const KEY = 'cache.setting.AccountSettingsDto';

    public function __construct(private CacheInterface $cache)
    {
    }

    /** @param callable(): AccountSettingsDto $loader */
    public function get(callable $loader): AccountSettingsDto
    {
        $settings = $this->cache->get(self::KEY, static function (CacheItemInterface $item) use ($loader): AccountSettingsDto {
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
