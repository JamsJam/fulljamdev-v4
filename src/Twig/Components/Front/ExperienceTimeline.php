<?php

namespace App\Twig\Components\Front;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Front:ExperienceTimeline', template: 'components/front/experience-timeline/ExperienceTimeline.html.twig')]
final class ExperienceTimeline
{
    public string $title = '';
    public string $titleLevel = 'h2';

    /** @var iterable<array-key, mixed> */
    public iterable $experiences = [];

    public ?string $id = null;
}
