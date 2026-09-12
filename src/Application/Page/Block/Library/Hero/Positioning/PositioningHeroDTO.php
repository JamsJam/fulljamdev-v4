<?php

namespace App\Application\Page\Block\Library\Hero\Positioning;

use App\Application\Page\Element\Cta\CtaDTO;
use App\Application\Page\Element\Heading\HeadingDTO;
use App\Application\Page\Element\Heading\HeadingLevel;
use App\Application\Page\Element\Image\ImageDTO;
use App\Application\Page\Element\Text\TextDTO;
use Symfony\Component\Validator\Constraints as Assert;

final class PositioningHeroDTO
{
    #[Assert\Valid] public HeadingDTO $title;
    #[Assert\Valid] public TextDTO $text;
    #[Assert\Valid] public ImageDTO $image;
    #[Assert\Valid] public ?CtaDTO $cta;
    public bool $reverse = false;
    /** @var list<SocialProofDTO> */
    #[Assert\Valid]
    #[Assert\Count(max: 2)]
    public array $proofs = [];
    /** @var list<PositioningCardDTO> */
    #[Assert\Valid]
    #[Assert\Count(exactly: 3, exactMessage: 'Le Hero de positionnement requiert exactement {{ limit }} cartes.')]
    public array $cards = [];

    public function __construct()
    {
        $this->title = new HeadingDTO();
        $this->title->level = HeadingLevel::H1;
        $this->text = new TextDTO();
        $this->image = new ImageDTO();
        $this->cta = new CtaDTO();
    }
}
