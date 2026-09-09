<?php

namespace App\Application\Page\Block\Library\Hero\Shared;

use App\Application\Page\Block\Interface\InitializableBlockDataInterface;
use App\Application\Page\Element\Badge\BadgeDTO;
use App\Application\Page\Element\Cta\CtaDTO;
use App\Application\Page\Element\Heading\HeadingDTO;
use App\Application\Page\Element\Image\ImageDTO;
use App\Application\Page\Element\Text\TextDTO;
use Symfony\Component\Validator\Constraints as Assert;

final class HeroDTO implements InitializableBlockDataInterface
{
    #[Assert\Valid]
    public HeadingDTO $title;
    #[Assert\Valid]
    public TextDTO $text;
    #[Assert\Valid]
    public ?CtaDTO $cta1;
    #[Assert\Valid]
    public ?CtaDTO $cta2;
    #[Assert\Valid]
    public ImageDTO $image;
    public bool $reverse = false;
    /** @var list<BadgeDTO> */
    #[Assert\Valid]
    #[Assert\Count(max: 3, maxMessage: 'Le Hero accepte au maximum {{ limit }} badges.')]
    public array $badges = [];

    public function __construct()
    {
        $this->title = new HeadingDTO();
        $this->text = new TextDTO();
        $this->cta1 = new CtaDTO();
        $this->cta2 = new CtaDTO();
        $this->image = new ImageDTO();
    }

    public function initializeDefaults(): void
    {
        $this->cta1 ??= new CtaDTO();
        $this->cta2 ??= new CtaDTO();
    }
}
