<?php

namespace App\Application\Blog\Article\Service;

use App\Application\Blog\Article\Provider\ArticleProvider;
use App\Entity\Content\Article;

final readonly class FindArticleService
{
    public function __construct(private ArticleProvider $provider)
    {
    }

    public function find(int $id): ?Article
    {
        return $this->provider->provideOne($id);
    }
}
