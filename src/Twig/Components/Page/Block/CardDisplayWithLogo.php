<?php

namespace App\Twig\Components\Page\Block;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'Page:Block:CardDisplay:WithLogo',
    template: 'components/page/block/card-display/DisplayCardWithLogo.html.twig',
)]
final class CardDisplayWithLogo extends AbstractCardDisplay
{
}
