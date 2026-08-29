<?php

namespace App\Application\Project\Service;

use App\Application\Project\Persister\ProjectPersister;
use App\Entity\Content\Project;

final readonly class DeleteProjectService
{
    public function __construct(private ProjectPersister $persister)
    {
    }

    public function delete(Project $project): void
    {
        $this->persister->remove($project);
    }
}
