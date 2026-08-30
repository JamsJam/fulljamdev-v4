<?php

namespace App\Application\Page\Block\Library\Blog\Latest;

use App\Application\Page\Block\Interface\BlockDefinitionInterface;

final class LatestArticlesBlock implements BlockDefinitionInterface
{
    public function type(): string
    {
        return 'blog.latest';
    }

    public function label(): string
    {
        return 'Derniers articles du blog';
    }

    public function category(): string
    {
        return 'Blog';
    }

    public function dtoClass(): string
    {
        return LatestArticlesDTO::class;
    }

    public function formType(): string
    {
        return LatestArticlesType::class;
    }

    public function component(): string
    {
        return 'Page:Block:Blog:Latest';
    }

    public function formTemplate(): string
    {
        return 'dashboard/page/block/_latest_articles_form.html.twig';
    }

    public function createDefaultData(): object
    {
        return new LatestArticlesDTO();
    }
}
