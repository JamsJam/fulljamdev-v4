<?php

namespace App\Application\Page\Block\Hero\Main;

use App\Application\Page\Block\Interface\BlockDefinitionInterface;

final class HeroBlock implements BlockDefinitionInterface
{
    public function type(): string
    {
        return 'hero.main';
    }

    public function label(): string
    {
        return 'Hero principal';
    }

    public function category(): string
    {
        return 'hero';
    }

    public function dtoClass(): string
    {
        return HeroDTO::class;
    }

    public function formType(): string
    {
        return HeroType::class;
    }

    public function component(): string
    {
        return 'Page:Block:Hero';
    }

    public function formTemplate(): string
    {
        return 'dashboard/page/block/_hero_form.html.twig';
    }

    public function createDefaultData(): object
    {
        return new HeroDTO();
    }
}
