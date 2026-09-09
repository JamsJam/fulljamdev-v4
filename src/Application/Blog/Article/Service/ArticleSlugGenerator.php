<?php

namespace App\Application\Blog\Article\Service;

use App\Application\Blog\Article\Provider\ArticleProvider;
use App\Entity\Blog\Article;
use App\Service\SluggerService;

final readonly class ArticleSlugGenerator
{
    private const MAX_LENGTH = 200;

    public function __construct(
        private SluggerService $slugger,
        private ArticleProvider $articles,
    ) {
    }

    public function generate(Article $article): void
    {
        if (null !== $article->getSlug() || null === $article->getTitle()) {
            return;
        }

        $base = $this->slugger->slugify($article->getTitle(), self::MAX_LENGTH);
        if ('' === $base) {
            $base = 'article';
        }
        $slug = $base;
        $suffix = 2;

        while ($this->articles->slugExists($slug, $article->getId())) {
            $ending = '-'.$suffix++;
            $slug = substr($base, 0, self::MAX_LENGTH - strlen($ending)).$ending;
        }

        $article->setSlug($slug);
    }
}
