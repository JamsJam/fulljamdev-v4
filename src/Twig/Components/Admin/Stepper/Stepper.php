<?php

namespace App\Twig\Components\Admin\Stepper;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Admin:Stepper', template: 'components/admin/stepper/Stepper.html.twig')]
final class Stepper
{
    /** @var list<string> */
    public array $steps = [];

    public int $activeStep = 1;

    public string $label = 'Étapes';
}
