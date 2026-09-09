<?php

namespace App\Application\Blog\Article\Service;

use App\Application\Blog\Article\Provider\ArticleProvider;
use App\Entity\Blog\Article;

final readonly class GetArticlesService
{
    public function __construct(private ArticleProvider $provider)
    {
    }

    /** @return list<Article> */
    public function get(): array
    {
        return $this->provider->provideAll();
    }
}
