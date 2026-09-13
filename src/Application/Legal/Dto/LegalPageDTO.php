<?php

namespace App\Application\Legal\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class LegalPageDTO
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 160)]
    public string $title = '';

    #[Assert\NotBlank]
    #[Assert\Length(max: 50000)]
    public string $content = '';
}
