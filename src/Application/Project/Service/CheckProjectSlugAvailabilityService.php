<?php

namespace App\Application\Project\Service;

use App\Application\Project\Provider\ProjectProvider;
use App\Entity\Content\Project;

final readonly class CheckProjectSlugAvailabilityService
{
    public function __construct(private ProjectProvider $provider)
    {
    }

    public function isUsed(string $slug, ?Project $project = null): bool
    {
        return $this->provider->slugExists($slug, $project?->getId());
    }
}
