<?php

namespace App\Twig\Components\Page\Block;

use App\Application\Page\Block\Library\Hero\Shared\HeroDTO;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'Page:Block:Hero:WithFsImage',
    template: 'components/page/block/hero/with-fs-image/HeroWithFsImage.html.twig',
)]
final class HeroWithFsImage extends AbstractHero
{
    public HeroDTO $data;
    public ?int $blockId = null;
}
