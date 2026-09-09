<?php

namespace App\Twig;

use App\Application\Settings\General\Dto\GeneralSettingsDto;
use App\Application\Settings\Service\GetGeneralSettingsService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class SiteSettingsExtension extends AbstractExtension
{
    public function __construct(private readonly GetGeneralSettingsService $settingsService)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('site_settings', $this->getSettings(...)),
        ];
    }

    public function getSettings(): GeneralSettingsDto
    {
        return $this->settingsService->get();
    }
}
