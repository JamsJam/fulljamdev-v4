<?php

namespace App\Twig\Components\Form;

use Symfony\Component\Form\FormView;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Form:Field', template: 'components/form/Field.html.twig')]
final class Field
{
    public FormView $field;
    public ?string $label = null;
    public ?string $description = null;
    public bool $showErrors = true;

    /** @var array<string, mixed> */
    public array $widgetAttr = [];
}
