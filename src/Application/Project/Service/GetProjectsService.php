<?php

namespace App\Application\Project\Service;

use App\Application\Project\Provider\ProjectProvider;
use App\Entity\Content\Project;

final readonly class GetProjectsService
{
    public function __construct(private ProjectProvider $provider)
    {
    }

    /** @return list<Project> */
    public function get(): array
    {
        return $this->provider->provideAll();
    }
}
