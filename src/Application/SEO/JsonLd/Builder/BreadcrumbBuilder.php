<?php

namespace App\Application\SEO\JsonLd\Builder;

use App\Application\SEO\JsonLd\Dto\BreadcrumbItem;
use App\Application\SEO\JsonLd\Dto\PageContext;

final readonly class BreadcrumbBuilder
{
    /** @return array<string, mixed>|null */
    public function build(PageContext $context): ?array
    {
        if ([] === $context->breadcrumbParents) {
            return null;
        }

        $items = [...$context->breadcrumbParents, new BreadcrumbItem($context->breadcrumbName ?? $context->title, $context->url)];
        $elements = [];
        foreach ($items as $index => $item) {
            $elements[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $item->name,
                'item' => $item->url,
            ];
        }

        return [
            '@type' => 'BreadcrumbList',
            '@id' => $context->id('breadcrumb'),
            'itemListElement' => $elements,
        ];
    }
}
