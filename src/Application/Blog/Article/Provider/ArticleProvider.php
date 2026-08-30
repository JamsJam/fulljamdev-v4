<?php

namespace App\Application\Blog\Article\Provider;

use App\Entity\Blog\Article;
use App\Repository\Blog\ArticleRepository;

final readonly class ArticleProvider
{
    public function __construct(private ArticleRepository $repository)
    {
    }

    /** @return list<Article> */
    public function provideAll(): array
    {
        return $this->repository->findBy([], ['createdAt' => 'DESC', 'id' => 'DESC']);
    }

    public function provideOne(int $id): ?Article
    {
        return $this->repository->find($id);
    }

    public function slugExists(?string $slug, ?int $exceptId = null): bool
    {
        if (null === $slug || '' === trim($slug)) {
            return false;
        }

        $article = $this->repository->findOneBy(['slug' => $slug]);

        return null !== $article && $article->getId() !== $exceptId;
    }
}
