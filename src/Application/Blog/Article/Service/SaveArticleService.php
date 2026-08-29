<?php

namespace App\Application\Blog\Article\Service;

use App\Application\Blog\Article\Dto\ArticleDto;
use App\Application\Blog\Article\Factory\ArticleFactory;
use App\Application\Blog\Article\Persister\ArticlePersister;
use App\Entity\Content\Article;

final readonly class SaveArticleService
{
    public function __construct(private ArticleFactory $factory, private ArticlePersister $persister)
    {
    }

    public function save(ArticleDto $dto, ?Article $article = null): Article
    {
        $article = $this->factory->create($dto, $article);
        $this->persister->persist($article);

        return $article;
    }
}
