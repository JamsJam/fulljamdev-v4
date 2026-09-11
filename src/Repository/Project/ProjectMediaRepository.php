<?php

namespace App\Repository\Project;

use App\Entity\Project\Project;
use App\Entity\Project\ProjectMedia;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<ProjectMedia> */
final class ProjectMediaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjectMedia::class);
    }

    public function attachReferencedMedia(Project $project): void
    {
        $content = $project->getContent();
        $hasChanges = false;

        foreach ($this->findBy(['project' => null]) as $media) {
            if (!str_contains($content, $media->getPath())) {
                continue;
            }

            $media->setProject($project);
            $hasChanges = true;
        }

        if ($hasChanges) {
            $this->getEntityManager()->flush();
        }
    }
}
