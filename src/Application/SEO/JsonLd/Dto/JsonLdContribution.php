<?php

namespace App\Application\SEO\JsonLd\Dto;

/** Public graph data only: no entities, repositories or block DTOs. */
final readonly class JsonLdContribution
{
    /**
     * @param list<array<string, mixed>> $nodes
     * @param list<string>               $mentions  IDs of secondary content mentioned by the page
     * @param list<string>               $questions IDs of FAQ questions presented on the page
     */
    public function __construct(
        public array $nodes = [],
        public array $mentions = [],
        public array $questions = [],
    ) {
    }
}
