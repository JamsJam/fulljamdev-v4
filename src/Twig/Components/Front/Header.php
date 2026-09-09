<?php

namespace App\Twig\Components\Front;

use App\Application\Settings\General\Dto\GeneralSettingsDto;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Front:Header', template: 'components/front/Header.html.twig')]
final class Header
{
    public GeneralSettingsDto $site;
}
