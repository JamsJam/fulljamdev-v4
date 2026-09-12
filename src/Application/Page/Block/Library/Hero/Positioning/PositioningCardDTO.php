<?php

namespace App\Application\Page\Block\Library\Hero\Positioning;

use Symfony\Component\Validator\Constraints as Assert;

final class PositioningCardDTO
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 120)]
    public string $title = '';

    #[Assert\NotBlank]
    #[Assert\Length(max: 500)]
    public string $text = '';
}
