<?php

namespace App\Twig\Components\Page\Block;

use App\Application\Page\Block\Library\Pricing\Main\PricingDTO;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Page:Block:Pricing:Main', template: 'components/page/block/pricing/Pricing.html.twig')] final class Pricing
{
    public PricingDTO $data;
    public ?int $blockId = null;

    public function formatPrice(int $cents): string
    {
        return number_format($cents / 100, 2, ',', "\u{202F}")."\u{00A0}€";
    }
}
