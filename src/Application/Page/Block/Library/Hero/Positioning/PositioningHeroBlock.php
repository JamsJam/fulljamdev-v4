<?php

namespace App\Application\Page\Block\Library\Hero\Positioning;

use App\Application\Page\Block\Interface\BlockDefinitionInterface;

final class PositioningHeroBlock implements BlockDefinitionInterface
{
    public function type(): string
    {
        return 'hero.positioning';
    }

    public function label(): string
    {
        return 'Hero de positionnement';
    }

    public function category(): string
    {
        return 'hero';
    }

    public function dtoClass(): string
    {
        return PositioningHeroDTO::class;
    }

    public function formType(): string
    {
        return PositioningHeroType::class;
    }

    public function component(): string
    {
        return 'Page:Block:Hero:Positioning';
    }

    public function formTemplate(): string
    {
        return 'dashboard/page/block/_positioning_hero_form.html.twig';
    }

    public function createDefaultData(): object
    {
        return new PositioningHeroDTO();
    }
}
