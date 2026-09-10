<?php

namespace App\Application\SEO\JsonLd\Builder;

use App\Application\SEO\JsonLd\Dto\PageContext;

final readonly class ContributionGraphAssembler
{
    /** @param list<array<string, mixed>> $nodes
     * @return list<array<string, mixed>>
     */
    public function assemble(array $nodes, PageContext $context): array
    {
        $mentions = [];
        $questions = [];
        foreach ($context->contributions as $contribution) {
            array_push($nodes, ...$contribution->nodes);
            array_push($mentions, ...$contribution->mentions);
            array_push($questions, ...$contribution->questions);
        }
        foreach ($nodes as &$node) {
            if (($node['@id'] ?? null) !== $context->id('webpage')) {
                continue;
            }
            if ([] !== $questions) {
                // A profile's main entity remains its person/organization.
                if (isset($node['mainEntity'])) {
                    array_push($mentions, ...$questions);
                } else {
                    $node['@type'] = array_values(array_unique([...(array) $node['@type'], 'FAQPage']));
                    $node['mainEntity'] = $this->references($questions);
                }
            }
            if ([] !== $mentions) {
                $node['mentions'] = $this->references($mentions);
            }
        }
        unset($node);

        $graph = [];
        foreach ($nodes as $node) {
            $id = $node['@id'] ?? null;
            if (null === $id) {
                $graph[] = $node;
            } else {
                // Keep the page definition authoritative; fill missing fields only.
                $graph[$id] = ($graph[$id] ?? []) + $node;
            }
        }

        return array_values($graph);
    }

    /** @param list<string> $ids
     * @return list<array{'@id': string}>
     */
    private function references(array $ids): array
    {
        return array_map(static fn (string $id): array => ['@id' => $id], array_values(array_unique($ids)));
    }
}
