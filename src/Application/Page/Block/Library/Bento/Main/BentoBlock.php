<?php

namespace App\Application\Page\Block\Library\Bento\Main;

use App\Application\Page\Block\Interface\BlockDefinitionInterface;

final class BentoBlock implements BlockDefinitionInterface
{
    public function type(): string
    {
        return 'bento.main';
    }

    public function label(): string
    {
        return 'Cartes Bento';
    }

    public function category(): string
    {
        return 'Contenu';
    }

    public function dtoClass(): string
    {
        return BentoDTO::class;
    }

    public function formType(): string
    {
        return BentoType::class;
    }

    public function component(): string
    {
        return 'Page:Block:Bento:Main';
    }

    public function formTemplate(): string
    {
        return 'dashboard/page/block/_bento_form.html.twig';
    }

    public function createDefaultData(): object
    {
        return new BentoDTO();
    }
}
