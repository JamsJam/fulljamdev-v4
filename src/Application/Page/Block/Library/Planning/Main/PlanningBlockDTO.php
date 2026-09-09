<?php

namespace App\Application\Page\Block\Library\Planning\Main;

use App\Application\Page\Element\Heading\HeadingDTO;
use App\Application\Page\Element\Heading\HeadingLevel;
use App\Application\Page\Element\Text\TextDTO;
use Symfony\Component\Validator\Constraints as Assert;

final class PlanningBlockDTO
{
    #[Assert\Valid]
    public HeadingDTO $title;

    #[Assert\Valid]
    public TextDTO $text;

    #[Assert\NotNull(message: 'Sélectionnez un planning.')]
    #[Assert\Positive(message: 'Le planning sélectionné est invalide.')]
    public ?int $planningId = null;

    public function __construct()
    {
        $this->title = new HeadingDTO();
        $this->title->level = HeadingLevel::H2;
        $this->text = new TextDTO();
    }
}
