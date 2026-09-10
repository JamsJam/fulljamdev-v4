<?php

namespace App\Application\SEO\JsonLd\Context;

use App\Application\SEO\JsonLd\Dto\PageContext;
use App\Application\SEO\JsonLd\Enum\PageType;
use App\Application\SEO\JsonLd\Interface\PageContextProviderInterface;
use App\Entity\Reservation\Planning;

final readonly class PlanningContextProvider implements PageContextProviderInterface
{
    public function __construct(private PublicUrlGenerator $urls)
    {
    }

    public function routes(): array
    {
        return ['app_front_planning_appointment'];
    }

    public function provide(string $route, array $view): ?PageContext
    {
        $planning = $view['planning'] ?? null;
        if (!$planning instanceof Planning || !$planning->isActive() || !$planning->isOnline()) {
            return null;
        }

        return new PageContext(
            type: PageType::PLANNING,
            url: $this->urls->route($route, ['slug' => $planning->getSlug()]),
            title: (string) $planning->getTitle(),
            description: trim(html_entity_decode(strip_tags($planning->getDescription() ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8')),
        );
    }
}
