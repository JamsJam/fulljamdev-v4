<?php

namespace App\Application\Settings\Service;

use App\Application\Settings\Account\Cache\AccountSettingsCache;
use App\Application\Settings\Account\Dto\AccountSettingsDto;
use App\Application\Settings\Account\Writer\AccountSettingsWriter;

final readonly class UpdateAccountSettingsService
{
    public function __construct(
        private AccountSettingsWriter $writer,
        private AccountSettingsCache $cache,
    ) {
    }

    public function update(AccountSettingsDto $dto): void
    {
        $this->writer->write($dto);
        $this->cache->invalidate();
    }
}
