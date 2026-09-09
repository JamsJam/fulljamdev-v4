<?php

namespace App\Application\Page\Element\Attribute;

use Symfony\Component\Validator\Constraints as Assert;

final class HtmlAttributeDTO
{
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: '/^[a-zA-Z][a-zA-Z0-9_-]*$/', message: 'Le nom de l’attribut est invalide.')]
    public string $name = '';

    #[Assert\NotBlank]
    public string $value = '';

    public function __construct(string $name = '', string $value = '')
    {
        $this->name = $name;
        $this->value = $value;
    }
}
