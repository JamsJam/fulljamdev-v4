<?php

namespace App\Application\Settings\Account\Proxy;

use App\Application\Settings\Account\Cache\AccountSettingsCache;
use App\Application\Settings\Account\Dto\AccountSettingsDto;
use App\Application\Settings\Account\Provider\AccountSettingsProvider;

final readonly class AccountSettingsProxy
{
    public function __construct(
        private AccountSettingsCache $cache,
        private AccountSettingsProvider $provider,
    ) {
    }

    public function get(): AccountSettingsDto
    {
        return $this->cache->get($this->provider->provide(...));
    }
}
