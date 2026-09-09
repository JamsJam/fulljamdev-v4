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
        $branding = is_array($configuration['branding'] ?? null) ? $configuration['branding'] : [];

        $dto = new GeneralSettingsDto();
        $maintenance = is_array($configuration['maintenance'] ?? null) ? $configuration['maintenance'] : [];
        $dto->maintenanceEnabled = true === ($maintenance['enabled'] ?? false);
        $dto->maintenanceMessage = $this->stringValue($maintenance, 'message', $dto->maintenanceMessage);
        foreach (is_array($maintenance['links'] ?? null) ? $maintenance['links'] : [] as $link) {
            if (is_array($link) && is_string($link['name'] ?? null) && is_string($link['value'] ?? null)
                && in_array(parse_url($link['value'], PHP_URL_SCHEME), ['https', 'http'], true)
                && false !== filter_var($link['value'], FILTER_VALIDATE_URL)) {
                $dto->maintenanceLinks[] = ['name' => $link['name'], 'value' => $link['value']];
            }
        }
        $dto->siteTitle = $this->stringValue($branding, 'site_title', 'FullJam Dev');
        $dto->logoPath = $this->nullableStringValue($branding, 'logo');
        $dto->faviconSvgPath = $this->nullableStringValue($branding, 'favicon_svg');
        $dto->faviconIcoPath = $this->nullableStringValue($branding, 'favicon_ico');
        $dto->appleTouchIconPath = $this->nullableStringValue($branding, 'apple_touch_icon');
        $dto->timezone = is_string($parameters['timezone'] ?? null) ? $parameters['timezone'] : 'Europe/Paris';
        $dto->homepagePageId = is_int($pages['homepage_id'] ?? null) ? $pages['homepage_id'] : null;

        return $dto;
    }

    /** @param array<string, mixed> $values */
    private function stringValue(array $values, string $key, string $default): string
    {
        return is_string($values[$key] ?? null) ? $values[$key] : $default;
    }

    /** @param array<string, mixed> $values */
    private function nullableStringValue(array $values, string $key): ?string
    {
        return is_string($values[$key] ?? null) && '' !== $values[$key] ? $values[$key] : null;
    }
}
