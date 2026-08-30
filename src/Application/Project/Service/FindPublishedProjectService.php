<?php

namespace App\Application\Project\Service;

use App\Entity\Project\Project;
use App\Repository\Project\ProjectRepository;
use Symfony\Component\Clock\ClockInterface;

final readonly class FindPublishedProjectService
{
    public function __construct(private ProjectRepository $projects, private ClockInterface $clock)
    {
    }

    public function findBySlug(string $slug): ?Project
    {
        return $this->projects->findPublishedBySlug($slug, \DateTimeImmutable::createFromInterface($this->clock->now()));
    }
}
