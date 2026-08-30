<?php

namespace App\Repository\Project;

use App\Entity\Project\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
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

    public function createPublishedCatalogQuery(\DateTimeImmutable $now, string $query = '', ?int $technologyId = null): QueryBuilder
    {
        $builder = $this->createQueryBuilder('project')
            ->andWhere('project.status = :status')
            ->andWhere('project.publishedAt IS NULL OR project.publishedAt <= :now')
            ->setParameter('status', 'published')
            ->setParameter('now', $now)
            ->orderBy('project.publishedAt', 'DESC')
            ->addOrderBy('project.id', 'DESC');

        if ('' !== $query) {
            $builder
                ->andWhere('LOWER(project.title) LIKE :query OR LOWER(project.excerpt) LIKE :query OR LOWER(project.content) LIKE :query')
                ->setParameter('query', '%'.mb_strtolower($query).'%');
        }

        if (null !== $technologyId) {
            $builder
                ->innerJoin('project.technologies', 'technology_filter')
                ->andWhere('technology_filter.id = :technology')
                ->setParameter('technology', $technologyId);
        }

        return $builder;
    }

    public function findPublishedBySlug(string $slug, \DateTimeImmutable $now): ?Project
    {
        return $this->createQueryBuilder('project')
            ->andWhere('project.slug = :slug')
            ->andWhere('project.status = :status')
            ->andWhere('project.publishedAt IS NULL OR project.publishedAt <= :now')
            ->setParameter('slug', $slug)
            ->setParameter('status', 'published')
            ->setParameter('now', $now)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
