<?php

namespace App\Application\SEO\JsonLd\Context;

use App\Application\SEO\JsonLd\Dto\ArticleData;
use App\Application\SEO\JsonLd\Dto\PageContext;
use App\Application\SEO\JsonLd\Enum\PageType;
use App\Application\SEO\JsonLd\Interface\PageContextProviderInterface;
use App\Entity\Blog\Article;

final readonly class BlogArticleContextProvider implements PageContextProviderInterface
{
    public function __construct(private PublicUrlGenerator $urls, private StructuredIdentityProvider $identity)
    {
    }

    public function routes(): array
    {
        return ['app_front_article_show'];
    }

    public function provide(string $route, array $view): ?PageContext
    {
        $article = $view['article'] ?? null;
        if (!$article instanceof Article) {
            return null;
        }

        return new PageContext(
            type: PageType::BLOG_ARTICLE,
            url: $this->urls->route($route, ['slug' => $article->getSlug()]),
            title: (string) $article->getTitle(),
            description: $article->getSummary(),
            images: $this->urls->images([$article->getCoverImage()]),
            data: new ArticleData(
                $article->getPublishedAt(),
                $article->getUpdatedAt(),
                $this->identity->isArticleAuthor() ? $this->identity->provide() : null,
            ),
        );
    }
}
