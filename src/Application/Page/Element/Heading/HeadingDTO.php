<?php

namespace App\Application\Page\Element\Heading;

use App\Application\Page\Element\Attribute\SafeHtmlAttributes;
use Symfony\Component\Validator\Constraints as Assert;

final class HeadingDTO
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 180)]
    public string $content = '';

    public HeadingLevel $level = HeadingLevel::H1;

    /** @var array<string, scalar|null> */
    #[SafeHtmlAttributes]
    public array $attributes = [];
}
