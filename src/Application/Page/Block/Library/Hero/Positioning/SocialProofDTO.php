<?php

namespace App\Application\Page\Block\Library\Hero\Positioning;

use Symfony\Component\Validator\Constraints as Assert;

final class SocialProofDTO
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 80)]
    public string $value = '';

    #[Assert\NotBlank]
    #[Assert\Length(max: 160)]
    public string $label = '';
}
