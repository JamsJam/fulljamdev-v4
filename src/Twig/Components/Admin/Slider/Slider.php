<?php

namespace App\Twig\Components\Admin\Slider;

use Symfony\Component\Form\FormView;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Admin:Slider', template: 'components/admin/slider/Slider.html.twig')]
final class Slider
{
    public FormView $field;
    public int $min = 0;
    public int $max = 100;
    public int $step = 1;
    public string $suffix = '';
}
