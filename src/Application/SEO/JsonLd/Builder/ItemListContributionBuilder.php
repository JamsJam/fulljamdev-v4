<?php

namespace App\Application\SEO\JsonLd\Builder;

use App\Application\SEO\JsonLd\Dto\JsonLdContribution;

final readonly class ItemListContributionBuilder
{
    /** @param list<array<string, mixed>> $items */
    public function build(string $id, string $title, array $items): JsonLdContribution
    {
        if ([] === $items) {
            return new JsonLdContribution();
        }
        $elements = [];
        foreach ($items as $index => $item) {
            $elements[] = ['@type' => 'ListItem', 'position' => $index + 1, 'item' => ['@id' => $item['@id']]];
        }
        $list = ['@type' => 'ItemList', '@id' => $id, 'numberOfItems' => count($elements), 'itemListElement' => $elements];
        if ('' !== trim($title)) {
            $list['name'] = $title;
        }

        return new JsonLdContribution(nodes: [$list, ...$items], mentions: [$id]);
    }
}
