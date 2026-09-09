<?php

namespace App\Twig\Components\Page;

use App\Application\Page\Block\Registry\BlockRegistry;
use App\Application\Page\Page\Dto\PageBlockDTO;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Page:BlockRenderer', template: 'components/page/BlockRenderer.html.twig')]
final class BlockRenderer
{
    public PageBlockDTO $block;

    public function __construct(private readonly BlockRegistry $registry)
    {
    }

    public function getComponent(): string
    {
        return $this->registry->get($this->block->type)->component();
    }
}
