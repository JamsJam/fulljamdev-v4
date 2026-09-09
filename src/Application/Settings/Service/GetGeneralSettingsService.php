<?php

namespace App\Application\Settings\Service;

use App\Application\Settings\General\Dto\GeneralSettingsDto;
use App\Application\Settings\General\Proxy\GeneralSettingsProxy;

final readonly class GetGeneralSettingsService
{
    public function __construct(private GeneralSettingsProxy $proxy)
    {
    }

    public function get(): GeneralSettingsDto
    {
        return $this->proxy->get();
    }
}
