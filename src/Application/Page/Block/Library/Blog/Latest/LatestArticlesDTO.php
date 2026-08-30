<?php

namespace App\Application\Page\Block\Library\Blog\Latest;

use App\Application\Page\Element\Heading\HeadingDTO;
use App\Application\Page\Element\Heading\HeadingLevel;
use App\Application\Page\Element\Text\TextDTO;
use Symfony\Component\Validator\Constraints as Assert;

final class LatestArticlesDTO
{
    #[Assert\Valid]
    public HeadingDTO $title;

    #[Assert\Valid]
    public TextDTO $text;

    public function __construct()
    {
        $this->title = new HeadingDTO();
        $this->title->content = 'Mon blog';
        $this->title->level = HeadingLevel::H2;

        $this->text = new TextDTO();
        $this->text->content = 'Conseils pratiques, retours d’expérience et réflexions autour de la création de produits web.';
    }
}
