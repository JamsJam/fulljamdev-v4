<?php

namespace App\Application\SEO\JsonLd\Context;

use App\Application\SEO\JsonLd\Dto\PageContext;
use App\Application\SEO\JsonLd\Enum\PageType;
use App\Application\SEO\JsonLd\Interface\PageContextProviderInterface;

final readonly class CatalogContextProvider implements PageContextProviderInterface
{
    // The catalogues have static headings in their public templates.
    private const CATALOGS = [
        'app_front_blog' => [PageType::BLOG_CATALOG, 'Le blog', 'articles'],
        'app_front_projects' => [PageType::PROJECT_CATALOG, 'Les projets', 'projects'],
    ];

    public function __construct(private PublicUrlGenerator $urls)
    {
    }

    public function routes(): array
    {
        return array_keys(self::CATALOGS);
    }

    public function provide(string $route, array $view): ?PageContext
    {
        [$type, $title, $key] = self::CATALOGS[$route];
        if (!isset($view[$key])) {
            return null;
        }

        // Match the existing canonical. Do not attach filtered/paginated items
        // to the identity of the whole catalogue.
        return new PageContext(type: $type, url: $this->urls->route($route), title: $title);
    }
}
