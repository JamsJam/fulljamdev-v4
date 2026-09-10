<?php

namespace App\Application\SEO\JsonLd\Contributor;

use App\Application\Page\Block\Library\Blog\Latest\LatestArticlesDTO;
use App\Application\Page\Block\Library\Blog\Latest\LatestArticlesProvider;
use App\Application\Page\Page\Dto\PageBlockDTO;
use App\Application\SEO\JsonLd\Builder\ItemListContributionBuilder;
use App\Application\SEO\JsonLd\Context\PublicUrlGenerator;
use App\Application\SEO\JsonLd\Dto\JsonLdContribution;
use App\Application\SEO\JsonLd\Interface\BlockJsonLdContributorInterface;

final readonly class LatestArticlesJsonLdContributor implements BlockJsonLdContributorInterface
{
    public function __construct(private LatestArticlesProvider $articles, private PublicUrlGenerator $urls, private ItemListContributionBuilder $lists)
    {
    }

    public function supports(PageBlockDTO $block): bool
    {
        return 'blog.latest' === $block->type && $block->data instanceof LatestArticlesDTO;
    }

    public function contribute(PageBlockDTO $block, string $blockId): JsonLdContribution
    {
        if (!$block->data instanceof LatestArticlesDTO) {
            return new JsonLdContribution();
        }
        $items = [];
        foreach ($this->articles->provide() as $article) {
            $url = $this->urls->route('app_front_article_show', ['slug' => $article->getSlug()]);
            $item = ['@type' => 'BlogPosting', '@id' => $url.'#article', 'url' => $url, 'headline' => $article->getTitle()];
            $images = $this->urls->images([$article->getCoverImage()]);
            if ([] !== $images) {
                $item['image'] = $images;
            }
            if (null !== $article->getPublishedAt()) {
                $item['datePublished'] = $article->getPublishedAt()->format(\DateTimeInterface::ATOM);
            }
            $items[] = $item;
        }

        return $this->lists->build($blockId.'-articles', $block->data->title->content, $items);
    }
}
