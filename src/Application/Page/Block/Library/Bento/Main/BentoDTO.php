<?php

namespace App\Application\Page\Block\Library\Bento\Main;

use App\Application\Page\Element\Heading\HeadingDTO;
use App\Application\Page\Element\Heading\HeadingLevel;
use App\Application\Page\Element\Text\TextDTO;
use Symfony\Component\Validator\Constraints as Assert;

final class BentoDTO
{
    #[Assert\Valid] public HeadingDTO $title;
    #[Assert\Valid] public TextDTO $text;
    /** @var list<BentoCardDTO> */ #[Assert\Valid] #[Assert\Count(min: 1)] public array $cards = [];
    public function __construct()
    {
        $this->title = new HeadingDTO();
        $this->title->level = HeadingLevel::H2;
        $this->text = new TextDTO();
    }
}
