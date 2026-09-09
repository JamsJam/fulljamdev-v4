<?php

namespace App\Application\Page\Block\Library\CardDisplay\DisplayCardWithImage;

use App\Application\Page\Block\Interface\BlockDefinitionInterface;
use App\Application\Page\Block\Library\CardDisplay\Shared\CardDisplayDTO;

final class DisplayCardWithImageBlock implements BlockDefinitionInterface
{
    public function type(): string
    {
        return 'card_display.with_image';
    }

    public function label(): string
    {
        return 'DisplayCardWithImage';
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
        return DisplayCardWithImageType::class;
    }

    public function component(): string
    {
        return 'Page:Block:CardDisplay:WithImage';
    }

    public function formTemplate(): string
    {
        return 'dashboard/page/block/_card_display_with_image_form.html.twig';
    }

    public function createDefaultData(): object
    {
        return new CardDisplayDTO();
    }
}
