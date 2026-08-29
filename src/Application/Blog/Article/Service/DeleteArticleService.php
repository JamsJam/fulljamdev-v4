<?php

namespace App\Application\Blog\Article\Service;

use App\Application\Blog\Article\Persister\ArticlePersister;
use App\Entity\Content\Article;

final readonly class DeleteArticleService
{
    public function __construct(private ArticlePersister $persister)
    {
    }

    public function delete(Article $article): void
    {
        $this->persister->remove($article);
    }
}
