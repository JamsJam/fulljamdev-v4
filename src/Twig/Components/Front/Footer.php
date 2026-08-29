<?php

namespace App\Twig\Components\Front;

use App\Application\Settings\General\Dto\GeneralSettingsDto;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Front:Footer', template: 'components/front/Footer.html.twig')]
final class Footer
{
    public GeneralSettingsDto $site;
}
