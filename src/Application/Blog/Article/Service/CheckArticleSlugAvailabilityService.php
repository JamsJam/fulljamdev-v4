<?php

namespace App\Application\Blog\Article\Service;

use App\Application\Blog\Article\Provider\ArticleProvider;
use App\Entity\Blog\Article;

final readonly class CheckArticleSlugAvailabilityService
{
    public function __construct(private ArticleProvider $provider)
    {
    }

    public function isUsed(?string $slug, ?Article $article = null): bool
    {
        return $this->provider->slugExists($slug, $article?->getId());
    }
}
