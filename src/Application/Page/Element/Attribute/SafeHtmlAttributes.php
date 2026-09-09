<?php

namespace App\Application\Page\Element\Attribute;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class SafeHtmlAttributes extends Constraint
{
    public string $message = 'L’attribut HTML « {{ attribute }} » n’est pas autorisé.';
}
