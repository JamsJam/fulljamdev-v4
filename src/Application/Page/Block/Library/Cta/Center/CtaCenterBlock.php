<?php

namespace App\Application\Page\Block\Library\Cta\Center;

use App\Application\Page\Block\Interface\BlockDefinitionInterface;

final class CtaCenterBlock implements BlockDefinitionInterface
{
    public function type(): string
    {
        return 'cta.main';
    }

    public function label(): string
    {
        return 'ctaCenter';
    }

    public function category(): string
    {
        return 'CTA';
    }

    public function dtoClass(): string
    {
        return CtaCenterDTO::class;
    }

    public function formType(): string
    {
        return CtaCenterType::class;
    }

    public function component(): string
    {
        return 'Page:Block:Cta:Center';
    }

    public function formTemplate(): string
    {
        return 'dashboard/page/block/_cta_form.html.twig';
    }

    public function createDefaultData(): object
    {
        return new CtaCenterDTO();
    }
}
