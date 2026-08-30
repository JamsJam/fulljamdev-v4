<?php

namespace App\Application\Blog\Article\Service;

use App\Entity\Blog\Article;
use App\Repository\Blog\ArticleRepository;
use Symfony\Component\Clock\ClockInterface;

final readonly class FindPublishedArticleService
{
    public function __construct(
        private ArticleRepository $articles,
        private ClockInterface $clock,
    ) {
    }

    public function findBySlug(string $slug): ?Article
    {
        return $this->articles->findPublishedBySlug(
            $slug,
            \DateTimeImmutable::createFromInterface($this->clock->now()),
        );
    }
}
