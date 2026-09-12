<?php

namespace App\Application\Page\Block\Library\Bento\Main;

use Symfony\Component\Validator\Constraints as Assert;

final class BentoCardDTO
{
    #[Assert\NotBlank] #[Assert\Length(max: 120)] public string $title = '';
    #[Assert\NotBlank] #[Assert\Length(max: 700)] public string $text = '';
    #[Assert\Choice(choices: ['standard', 'wide', 'tall'])] public string $size = 'standard';
}
