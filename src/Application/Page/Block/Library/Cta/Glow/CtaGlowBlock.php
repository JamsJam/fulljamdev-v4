<?php

namespace App\Application\Page\Block\Library\Cta\Glow;

use App\Application\Page\Block\Interface\BlockDefinitionInterface;
use App\Application\Page\Block\Library\Cta\Center\CtaCenterDTO;
use App\Application\Page\Block\Library\Cta\Center\CtaCenterType;

final class CtaGlowBlock implements BlockDefinitionInterface
{
    public function type(): string
    {
        return 'cta.glow';
    }

    public function label(): string
    {
        return 'ctaGlow';
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
        return 'Page:Block:Cta:Glow';
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
