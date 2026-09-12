<?php

namespace App\Application\Page\Block\Library\Pricing\Main;

use App\Application\Page\Block\Interface\BlockDefinitionInterface;

final class PricingBlock implements BlockDefinitionInterface
{
    public function type(): string
    {
        return 'pricing.main';
    }

    public function label(): string
    {
        return 'Tarifs';
    }

    public function category(): string
    {
        return 'Contenu';
    }

    public function dtoClass(): string
    {
        return PricingDTO::class;
    }

    public function formType(): string
    {
        return PricingType::class;
    }

    public function component(): string
    {
        return 'Page:Block:Pricing:Main';
    }

    public function formTemplate(): string
    {
        return 'dashboard/page/block/_pricing_form.html.twig';
    }

    public function createDefaultData(): object
    {
        return new PricingDTO();
    }
}
