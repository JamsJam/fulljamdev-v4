<?php

namespace App\Twig\Components\Form;

use Symfony\Component\Form\FormView;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Form:Dropzone', template: 'components/form/Dropzone.html.twig')]
final class Dropzone
{
    public FormView $field;
    public ?string $currentImage = null;
    /** @var list<string> */
    public array $currentImages = [];
}
