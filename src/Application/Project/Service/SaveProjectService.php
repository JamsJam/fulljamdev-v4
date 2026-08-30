<?php

namespace App\Application\Project\Service;

use App\Application\Project\Dto\ProjectDto;
use App\Application\Project\Factory\ProjectFactory;
use App\Application\Project\Persister\ProjectPersister;
use App\Entity\Project\Project;

final readonly class SaveProjectService
{
    public function __construct(private ProjectFactory $factory, private ProjectSlugGenerator $slugGenerator, private ProjectPersister $persister)
    {
    }

    public function save(ProjectDto $dto, ?Project $project = null): Project
    {
        $project = $this->factory->create($dto, $project);
        $this->slugGenerator->generate($project);
        $this->persister->persist($project);

        return $project;
    }
}
