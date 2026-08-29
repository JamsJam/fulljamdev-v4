<?php

namespace App\Application\Page\Block\Library\CardDisplay\Data;

interface FeaturedProjectsProviderInterface
{
    /** @return list<CardDisplayItemDTO> */
    public function provide(): array;
}
