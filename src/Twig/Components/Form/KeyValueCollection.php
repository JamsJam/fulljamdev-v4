<?php

namespace App\Twig\Components\Form;

use Symfony\Component\Form\FormView;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Form:KeyValueCollection', template: 'components/form/KeyValueCollection.html.twig')]
final class KeyValueCollection
{
    public FormView $field;
    public ?string $legend = null;
    public string $keyLabel = 'Attribut';
    public string $valueLabel = 'Valeur';
    public string $addLabel = 'Ajouter un attribut';
}
