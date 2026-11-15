<?php

namespace App\Application\Settings\Service;

use App\Application\Settings\General\Dto\GeneralSettingsDto;
use App\Application\Settings\General\Proxy\GeneralSettingsProxy;
use App\Application\QrVisitCard\Provider\QrVisitCardProvider;

final readonly class GetGeneralSettingsService
{
    public function __construct(
        private GeneralSettingsProxy $proxy,
        private QrVisitCardProvider $qrVisitCardProvider,
    ) {
    }

    public function get(): GeneralSettingsDto
    {
        $settings = $this->proxy->get();
        $settings->qrVisitCardPath = $this->qrVisitCardProvider->provide()?->getPath();

        return $settings;
    }
}
