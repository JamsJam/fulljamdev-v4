<?php

namespace App\Application\Page\Element\Text;

use App\Application\Page\Element\Attribute\SafeHtmlAttributes;
use Symfony\Component\Validator\Constraints as Assert;

final class TextDTO
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 1000)]
    public string $content = '';
    /** @var array<string, scalar|null> */
    #[SafeHtmlAttributes]
    public array $attributes = [];
}
