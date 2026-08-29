<?php

namespace App\Twig\Components\Page\Block;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'Page:Block:CardDisplay:WithImage',
    template: 'components/page/block/card-display/DisplayCardWithImage.html.twig',
)]
final class CardDisplayWithImage extends AbstractCardDisplay
{
}
