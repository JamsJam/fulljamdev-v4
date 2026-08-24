<?php

namespace App\Application\Page\Element\Badge;

use Symfony\Component\Validator\Constraints as Assert;

final class BadgeDTO
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 80)]
    public string $label = '';
}
