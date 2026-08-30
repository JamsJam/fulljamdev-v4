<?php

namespace App\Application\Blog\Workflow\Rule;

use App\Entity\Blog\Article;
use Symfony\Component\Clock\ClockInterface;

final readonly class ArticleCanBeScheduledRule
{
    public function __construct(private ClockInterface $clock)
    {
    }

    /** @return list<string> */
    public function violations(Article $article): array
    {
        $publishedAt = $article->getPublishedAt();

        if (null === $publishedAt) {
            return ['Une date de publication est obligatoire pour planifier l’article.'];
        }

        if ($publishedAt <= $this->clock->now()) {
            return ['La date de publication planifiée doit être dans le futur.'];
        }

        return [];
    }
}
