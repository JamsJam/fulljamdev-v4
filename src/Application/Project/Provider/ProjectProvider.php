<?php

namespace App\Application\Project\Provider;

use App\Entity\Project\Project;
use App\Repository\Project\ProjectRepository;

final readonly class ProjectProvider
{
    public function __construct(private ProjectRepository $repository)
    {
    }

    /** @return list<Project> */
    public function provideAll(): array
    {
        return $this->repository->findBy([], ['createdAt' => 'DESC', 'id' => 'DESC']);
    }

    /** @return list<Project> */
    public function provideFeatured(): array
    {
        return $this->repository->findPublishedFeatured();
    }

    public function provideOne(int $id): ?Project
    {
        return $this->repository->find($id);
    }

    public function slugExists(string $slug, ?int $exceptId = null): bool
    {
        $project = $this->repository->findOneBy(['slug' => $slug]);

        return null !== $project && $project->getId() !== $exceptId;
    }
}
