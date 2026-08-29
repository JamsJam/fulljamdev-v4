<?php

namespace App\Repository\Content;

use App\Entity\Content\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Project> */
final class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    /** @return list<Project> */
    public function findPublishedFeatured(): array
    {
        return $this->createQueryBuilder('project')
            ->andWhere('project.isFeatured = :featured')
            ->andWhere('project.status = :status')
            ->andWhere('project.publishedAt IS NULL OR project.publishedAt <= :now')
            ->setParameter('featured', true)
            ->setParameter('status', 'published')
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('project.publishedAt', 'DESC')
            ->addOrderBy('project.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
