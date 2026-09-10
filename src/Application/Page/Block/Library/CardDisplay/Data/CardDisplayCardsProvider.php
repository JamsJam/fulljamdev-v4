<?php

namespace App\Application\Page\Block\Library\CardDisplay\Data;

use App\Application\Page\Block\Library\CardDisplay\Shared\CardDisplayDTO;
use App\Application\Page\Data\Enum\ValueSource;

final readonly class CardDisplayCardsProvider
{
    public function __construct(private FeaturedProjectsProviderInterface $projects)
    {
    }

    /** @return list<CardDisplayItemDTO> */
    public function provide(CardDisplayDTO $data): array
    {
        if (ValueSource::STATIC === $data->source) {
            return $data->cards;
        }

        return 'featured_projects' === $data->sourceKey ? $this->projects->provide() : [];
    }
}
