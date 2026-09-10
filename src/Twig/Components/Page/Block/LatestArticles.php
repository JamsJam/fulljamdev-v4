<?php

namespace App\Twig\Components\Page\Block;

use App\Application\Page\Block\Library\Blog\Latest\LatestArticlesDTO;
use App\Application\Page\Block\Library\Blog\Latest\LatestArticlesProvider;
use App\Entity\Blog\Article;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'Page:Block:Blog:Latest',
    template: 'components/page/block/blog/LatestArticles.html.twig',
)]
final class LatestArticles
{
    public LatestArticlesDTO $data;
    public ?int $blockId = null;

    public function __construct(private readonly LatestArticlesProvider $articles)
    {
    }

    /** @return list<Article> */
    public function getArticles(): array
    {
        return $this->articles->provide();
    }
}
