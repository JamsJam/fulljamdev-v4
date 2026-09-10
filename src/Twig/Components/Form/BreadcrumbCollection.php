<?php

namespace App\Twig\Components\Form;

use Symfony\Component\Form\FormView;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Form:BreadcrumbCollection', template: 'components/form/BreadcrumbCollection.html.twig')]
final class BreadcrumbCollection
{
    public FormView $field;
}
