<?php

namespace App\Application\Blog\Workflow\Rule;

use App\Application\Blog\Workflow\Enum\ArticleStatus;
use App\Entity\Blog\Article;
use Symfony\Component\Clock\ClockInterface;

final readonly class ArticleCanBePublishedRule
{
    public function __construct(private ClockInterface $clock)
    {
    }

    /** @return list<string> */
    public function violations(Article $article): array
    {
        if (ArticleStatus::SCHEDULED !== $article->getStatus()) {
            return [];
        }

        $publishedAt = $article->getPublishedAt();
        if (null === $publishedAt || $publishedAt > $this->clock->now()) {
            return ['La date de publication planifiée n’est pas encore atteinte.'];
        }

        return [];
    }
}
