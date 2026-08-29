<?php

namespace App\Twig\Components\Page\Block;

use App\Application\Page\Block\Library\Hero\Shared\HeroDTO;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'Page:Block:Hero:ClassicSquare',
    template: 'components/page/block/hero/classic-square/Hero.html.twig',
)]
final class HeroClassicSquare extends AbstractHero
{
    public HeroDTO $data;
    public ?int $blockId = null;
}
