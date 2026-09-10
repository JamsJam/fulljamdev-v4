<?php

namespace App\Application\Page\Block\Library\Blog\Latest;

use App\Entity\Blog\Article;
use App\Repository\Blog\ArticleRepository;
use Symfony\Component\HttpFoundation\RequestStack;

final readonly class LatestArticlesProvider
{
    public function __construct(private ArticleRepository $articles, private RequestStack $requests)
    {
    }

    /** @return list<Article> */
    public function provide(): array
    {
        $request = $this->requests->getCurrentRequest();
        if ($request?->attributes->has(self::class)) {
            return $request->attributes->get(self::class);
        }
        $articles = $this->articles->findLatestPublished(new \DateTimeImmutable(), 4);
        $request?->attributes->set(self::class, $articles);

        return $articles;
    }
}
