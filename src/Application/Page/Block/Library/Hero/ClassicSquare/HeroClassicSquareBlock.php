<?php

namespace App\Application\Page\Block\Library\Hero\ClassicSquare;

use App\Application\Page\Block\Interface\BlockDefinitionInterface;
use App\Application\Page\Block\Library\Hero\Shared\HeroDTO;
use App\Application\Page\Block\Library\Hero\Shared\HeroType;

final class HeroClassicSquareBlock implements BlockDefinitionInterface
{
    public function type(): string
    {
        return 'hero.main';
    }

    public function label(): string
    {
        return 'Hero classique avec image carrée';
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
        return 'Page:Block:Hero:ClassicSquare';
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
