<?php

namespace App\Application\Page\Block\Library\Project\Featured;

use App\Application\Page\Element\Heading\HeadingDTO;
use App\Application\Page\Element\Heading\HeadingLevel;
use App\Application\Page\Element\Text\TextDTO;
use Symfony\Component\Validator\Constraints as Assert;

final class FeaturedProjectsDTO
{
    #[Assert\Valid]
    public HeadingDTO $title;

    #[Assert\Valid]
    public TextDTO $text;

    public function __construct()
    {
        $this->title = new HeadingDTO();
        $this->title->content = 'Mes projets';
        $this->title->level = HeadingLevel::H2;

        $this->text = new TextDTO();
        $this->text->content = 'Découvrez une sélection de projets conçus pour répondre à des besoins concrets.';
    }
}
