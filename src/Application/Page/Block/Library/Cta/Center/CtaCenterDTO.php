<?php

namespace App\Application\Page\Block\Library\Cta\Center;

use App\Application\Page\Element\Cta\CtaDTO;
use App\Application\Page\Element\Heading\HeadingDTO;
use App\Application\Page\Element\Heading\HeadingLevel;
use App\Application\Page\Element\Text\TextDTO;
use Symfony\Component\Validator\Constraints as Assert;

final class CtaCenterDTO
{
    #[Assert\Valid]
    public HeadingDTO $title;

    #[Assert\Valid]
    public TextDTO $text;

    #[Assert\Valid]
    public CtaDTO $cta;

    public function __construct()
    {
        $this->title = new HeadingDTO();
        $this->title->level = HeadingLevel::H2;
        $this->text = new TextDTO();
        $this->cta = new CtaDTO();
    }
}
