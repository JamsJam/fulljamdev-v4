<?php

namespace App\Application\Settings\General\Writer;

use App\Application\Settings\General\Dto\GeneralSettingsDto;
use App\Application\Settings\Storage\YamlSettingsStorage;

final readonly class GeneralSettingsWriter
{
    public function __construct(private YamlSettingsStorage $storage)
    {
    }

    public function write(GeneralSettingsDto $dto): void
    {
        $configuration = $this->storage->read();
        $parameters = is_array($configuration['parameters'] ?? null) ? $configuration['parameters'] : [];
        $pages = is_array($configuration['pages'] ?? null) ? $configuration['pages'] : [];
        $parameters['timezone'] = $dto->timezone;
        $pages['homepage_id'] = $dto->homepagePageId;
        $configuration['branding'] = [
            'site_title' => $dto->siteTitle,
            'logo' => $dto->logoPath,
            'favicon_svg' => $dto->faviconSvgPath,
            'favicon_ico' => $dto->faviconIcoPath,
            'apple_touch_icon' => $dto->appleTouchIconPath,
        ];
        $configuration['parameters'] = $parameters;
        $configuration['pages'] = $pages;

        $this->storage->write($configuration);
    }
}
