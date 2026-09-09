<?php

namespace App\Application\Project\Persister;

use App\Entity\Project\Project;
use Doctrine\ORM\EntityManagerInterface;

final readonly class ProjectPersister
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function persist(Project $project): void
    {
        $this->entityManager->persist($project);
        $this->entityManager->flush();
    }

    public function remove(Project $project): void
    {
        $this->entityManager->remove($project);
        $this->entityManager->flush();
    }
}
