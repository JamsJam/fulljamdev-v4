<?php

namespace App\Application\SEO\JsonLd\Builder;

use App\Application\SEO\JsonLd\Dto\PageContext;

final readonly class WebPageBuilder
{
    /** @return array<string, mixed> */
    public function build(PageContext $context, string $schemaType = 'WebPage'): array
    {
        $node = [
            '@type' => $schemaType,
            '@id' => $context->id('webpage'),
            'url' => $context->url,
            'name' => $context->title,
        ];
        if (null !== $context->description && '' !== trim($context->description)) {
            $node['description'] = $context->description;
        }
        if ([] !== $context->images) {
            $node['image'] = $context->images;
        }

        return $node;
    }
}
