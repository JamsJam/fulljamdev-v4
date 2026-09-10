<?php

namespace App\Application\SEO\JsonLd\Registry;

use App\Application\Page\Page\Dto\PageBlockDTO;
use App\Application\SEO\JsonLd\Dto\JsonLdContribution;
use App\Application\SEO\JsonLd\Interface\BlockJsonLdContributorInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

final readonly class BlockJsonLdCollector
{
    /** @param iterable<BlockJsonLdContributorInterface> $contributors */
    public function __construct(
        #[AutowireIterator('app.json_ld.block_contributor')] private iterable $contributors,
    ) {
    }

    /**
     * @param array<int, PageBlockDTO> $blocks Rendered page blocks, in display order
     *
     * @return list<JsonLdContribution>
     */
    public function collect(array $blocks, string $pageUrl): array
    {
        $result = [];
        foreach (array_values($blocks) as $index => $block) {
            $id = $pageUrl.'#block-'.($block->id ?? 'position-'.$index);
            foreach ($this->contributors as $contributor) {
                if ($contributor->supports($block)) {
                    $result[] = $contributor->contribute($block, $id);
                }
            }
        }

        return $result;
    }
}
