<?php

namespace App\Application\Blog\Workflow\Event;

use App\Entity\Blog\Article;

final readonly class ArticleArchivedEvent
{
    public function __construct(public Article $article)
    {
    }
}
