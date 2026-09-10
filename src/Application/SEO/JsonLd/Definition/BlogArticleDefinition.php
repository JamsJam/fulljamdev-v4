<?php

namespace App\Application\SEO\JsonLd\Definition;

use App\Application\SEO\JsonLd\Builder\IdentityBuilder;
use App\Application\SEO\JsonLd\Dto\ArticleData;
use App\Application\SEO\JsonLd\Dto\PageContext;
use App\Application\SEO\JsonLd\Enum\PageType;
use App\Application\SEO\JsonLd\Interface\PageDefinitionInterface;

final readonly class BlogArticleDefinition implements PageDefinitionInterface
{
    public function __construct(private IdentityBuilder $identity)
    {
    }

    public function types(): array
    {
        return [PageType::BLOG_ARTICLE];
    }

    public function build(PageContext $context): array
    {
        $article = $context->data;
        if (!$article instanceof ArticleData) {
            throw new \InvalidArgumentException('A blog article requires ArticleData.');
        }

        $node = [
            '@type' => 'BlogPosting',
            '@id' => $context->id('article'),
            'url' => $context->url,
            'mainEntityOfPage' => $context->url,
            'headline' => $context->title,
        ];
        if (null !== $context->description && '' !== trim($context->description)) {
            $node['description'] = $context->description;
        }
        if ([] !== $context->images) {
            $node['image'] = $context->images;
        }
        if (null !== $article->publishedAt) {
            $node['datePublished'] = $article->publishedAt->format(\DateTimeInterface::ATOM);
        }
        if (null !== $article->modifiedAt) {
            $node['dateModified'] = $article->modifiedAt->format(\DateTimeInterface::ATOM);
        }
        if (null !== $article->author) {
            $node['author'] = $this->identity->build($article->author);
        }

        return [$node];
    }
}
