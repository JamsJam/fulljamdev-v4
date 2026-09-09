<?php

namespace App\Application\Page\Block\Asset;

interface PageBlockDataProviderInterface
{
    /** @return iterable<array<string, mixed>> */
    public function provide(): iterable;
}
