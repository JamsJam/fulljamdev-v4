<?php

namespace App\Application\Project\Service;

use App\Application\Project\Provider\ProjectProvider;
use App\Entity\Project\Project;

final readonly class FindProjectService
{
    public function __construct(private ProjectProvider $provider)
    {
    }

    public function find(int $id): ?Project
    {
        return $this->provider->provideOne($id);
    }
}
