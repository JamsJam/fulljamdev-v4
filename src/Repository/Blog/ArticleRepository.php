<?php

namespace App\Repository\Blog;

use App\Application\Blog\Workflow\Enum\ArticleStatus;
use App\Entity\Blog\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Article> */
final class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /** @return list<Article> */
    public function findActiveScheduled(): array
    {
        return $this->createQueryBuilder('article')
            ->andWhere('article.status = :status')
            ->andWhere('article.archivedAt IS NULL')
            ->setParameter('status', ArticleStatus::SCHEDULED->value)
            ->orderBy('article.publishedAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findPublishedBySlug(string $slug, \DateTimeImmutable $now): ?Article
    {
        return $this->createQueryBuilder('article')
            ->andWhere('article.slug = :slug')
            ->andWhere('article.status = :status')
            ->andWhere('article.archivedAt IS NULL')
            ->andWhere('article.publishedAt IS NOT NULL')
            ->andWhere('article.publishedAt <= :now')
            ->setParameter('slug', $slug)
            ->setParameter('status', ArticleStatus::PUBLISHED->value)
            ->setParameter('now', $now)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /** @return list<Article> */
    public function findLatestPublished(\DateTimeImmutable $now, int $limit = 4): array
    {
        return $this->createQueryBuilder('article')
            ->leftJoin('article.category', 'category')
            ->addSelect('category')
            ->andWhere('article.status = :status')
            ->andWhere('article.archivedAt IS NULL')
            ->andWhere('article.publishedAt IS NOT NULL')
            ->andWhere('article.publishedAt <= :now')
            ->setParameter('status', ArticleStatus::PUBLISHED->value)
            ->setParameter('now', $now)
            ->orderBy('article.publishedAt', 'DESC')
            ->addOrderBy('article.id', 'DESC')
            ->setMaxResults(max(1, $limit))
            ->getQuery()
            ->getResult();
    }

    public function createPublishedCatalogQuery(\DateTimeImmutable $now, string $query = '', string $categorySlug = ''): QueryBuilder
    {
        $builder = $this->createQueryBuilder('article')
            ->leftJoin('article.category', 'category')
            ->addSelect('category')
            ->andWhere('article.status = :status')
            ->andWhere('article.archivedAt IS NULL')
            ->andWhere('article.publishedAt IS NOT NULL')
            ->andWhere('article.publishedAt <= :now')
            ->setParameter('status', ArticleStatus::PUBLISHED->value)
            ->setParameter('now', $now)
            ->orderBy('article.publishedAt', 'DESC');

        if ('' !== $query) {
            $builder
                ->andWhere('LOWER(article.title) LIKE :query OR LOWER(article.summary) LIKE :query OR LOWER(article.content) LIKE :query')
                ->setParameter('query', '%'.mb_strtolower($query).'%');
        }

        if ('' !== $categorySlug) {
            $builder->andWhere('category.slug = :category')->setParameter('category', $categorySlug);
        }

        return $builder;
    }
}
