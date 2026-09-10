<?php

namespace App\Application\SEO\JsonLd\Context;

use App\Application\SEO\JsonLd\Dto\PageContext;
use App\Application\SEO\JsonLd\Enum\PageType;
use App\Application\SEO\JsonLd\Interface\PageContextProviderInterface;
use App\Entity\Project\Project;

final readonly class ProjectContextProvider implements PageContextProviderInterface
{
    public function __construct(private PublicUrlGenerator $urls)
    {
    }

    public function routes(): array
    {
        return ['app_front_project_show'];
    }

    public function provide(string $route, array $view): ?PageContext
    {
        $project = $view['project'] ?? null;
        if (!$project instanceof Project) {
            return null;
        }

        $images = [];
        foreach ($project->getImages() as $image) {
            $images[] = $image->getPath();
        }

        return new PageContext(
            type: PageType::PROJECT,
            url: $this->urls->route($route, ['slug' => $project->getSlug()]),
            title: $project->getTitle(),
            description: $project->getExcerpt(),
            images: $this->urls->images($images),
        );
    }
}
