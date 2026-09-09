<?php

namespace App\Twig\Components\Front;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Front:ExperienceItem', template: 'components/front/experience-timeline/ExperienceItem.html.twig')]
final class ExperienceItem
{
    public mixed $experience;
}
