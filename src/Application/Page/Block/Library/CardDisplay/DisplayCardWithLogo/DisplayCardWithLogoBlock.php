<?php

namespace App\Application\Page\Block\Library\CardDisplay\DisplayCardWithLogo;

use App\Application\Page\Block\Interface\BlockDefinitionInterface;
use App\Application\Page\Block\Library\CardDisplay\Shared\CardDisplayDTO;

final class DisplayCardWithLogoBlock implements BlockDefinitionInterface
{
    public function type(): string
    {
        return 'services.main';
    }

    public function label(): string
    {
        return 'DisplayCardWithLogo';
    }

    public function category(): string
    {
        return 'CardDisplay';
    }

    public function dtoClass(): string
    {
        return CardDisplayDTO::class;
    }

    public function formType(): string
    {
        return DisplayCardWithLogoType::class;
    }

    public function component(): string
    {
        return 'Page:Block:CardDisplay:WithLogo';
    }

    public function formTemplate(): string
    {
        return 'dashboard/page/block/_card_display_with_logo_form.html.twig';
    }

    public function createDefaultData(): object
    {
        return new CardDisplayDTO();
    }
}
