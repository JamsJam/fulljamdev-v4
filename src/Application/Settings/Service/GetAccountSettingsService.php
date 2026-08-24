<?php

namespace App\Application\Settings\Service;

use App\Application\Settings\Account\Dto\AccountSettingsDto;
use App\Application\Settings\Account\Proxy\AccountSettingsProxy;

final readonly class GetAccountSettingsService
{
    public function __construct(private AccountSettingsProxy $proxy)
    {
    }

    public function get(): AccountSettingsDto
    {
        return $this->proxy->get();
    }
}
