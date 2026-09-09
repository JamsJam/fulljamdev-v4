<?php

namespace App\Application\Page\Block\Asset;

use App\Repository\Page\PageBlockRepository;

final readonly class DoctrinePageBlockDataProvider implements PageBlockDataProviderInterface
{
    public function __construct(private PageBlockRepository $blocks)
    {
    }

    public function provide(): iterable
    {
        foreach ($this->blocks->findAll() as $block) {
            yield $block->getData();
        }
    }
}
