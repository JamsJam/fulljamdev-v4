<?php

namespace App\Twig\Components\Page\Block;

use App\Application\Page\Block\Library\Hero\Positioning\PositioningHeroDTO;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Page:Block:Hero:Positioning', template: 'components/page/block/hero/positioning/Hero.html.twig')] final class PositioningHero extends AbstractHero
{
    public PositioningHeroDTO $data;
    public ?int $blockId = null;
}
