<?php

namespace App\Application\Page\Block\Library\Faq\Main;

use Symfony\Component\Validator\Constraints as Assert;

final class FaqItemDTO
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 240)]
    public string $question = '';

    #[Assert\NotBlank]
    #[Assert\Length(max: 2000)]
    public string $answer = '';
}
