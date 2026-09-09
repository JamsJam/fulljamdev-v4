<?php

namespace App\Application\Page\Block\Library\Faq\Main;

use App\Application\Page\Block\Interface\BlockDefinitionInterface;

final class FaqBlock implements BlockDefinitionInterface
{
    public function type(): string
    {
        return 'faq.main';
    }

    public function label(): string
    {
        return 'FAQ avec accordéon';
    }

    public function category(): string
    {
        return 'FAQ';
    }

    public function dtoClass(): string
    {
        return FaqDTO::class;
    }

    public function formType(): string
    {
        return FaqType::class;
    }

    public function component(): string
    {
        return 'Page:Block:Faq:Main';
    }

    public function formTemplate(): string
    {
        return 'dashboard/page/block/_faq_form.html.twig';
    }

    public function createDefaultData(): object
    {
        $data = new FaqDTO();
        $data->items = [new FaqItemDTO()];

        return $data;
    }
}
