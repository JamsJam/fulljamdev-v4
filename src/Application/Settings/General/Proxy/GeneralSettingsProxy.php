<?php

namespace App\Application\Settings\General\Proxy;

use App\Application\Settings\General\Cache\GeneralSettingsCache;
use App\Application\Settings\General\Dto\GeneralSettingsDto;
use App\Application\Settings\General\Provider\GeneralSettingsProvider;

final readonly class GeneralSettingsProxy
{
    public function __construct(
        private GeneralSettingsCache $cache,
        private GeneralSettingsProvider $provider,
    ) {
    }

    public function get(): GeneralSettingsDto
    {
        return $this->cache->get($this->provider->provide(...));
    }
}
