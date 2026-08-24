<?php

namespace App\Application\Settings\General\Provider;

use App\Application\Settings\General\Dto\GeneralSettingsDto;
use App\Application\Settings\Storage\YamlSettingsStorage;

final readonly class GeneralSettingsProvider
{
    public function __construct(private YamlSettingsStorage $storage)
    {
    }

    public function provide(): GeneralSettingsDto
    {
        $configuration = $this->storage->read();
        $parameters = is_array($configuration['parameters'] ?? null) ? $configuration['parameters'] : [];
        $pages = is_array($configuration['pages'] ?? null) ? $configuration['pages'] : [];

        $dto = new GeneralSettingsDto();
        $dto->timezone = is_string($parameters['timezone'] ?? null) ? $parameters['timezone'] : 'Europe/Paris';
        $dto->homepagePageId = is_int($pages['homepage_id'] ?? null) ? $pages['homepage_id'] : null;

        return $dto;
    }
}
