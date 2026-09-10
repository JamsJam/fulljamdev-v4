<?php

namespace App\Application\SEO\JsonLd\Dto;

final readonly class IdentityData
{
    public function __construct(
        public string $type,
        public string $name,
        public string $id,
        public ?string $url = null,
        public ?string $logo = null,
        /** @var list<string> */
        public array $sameAs = [],
    ) {
        if (!in_array($type, ['Person', 'Organization'], true)) {
            throw new \InvalidArgumentException('A structured identity must be a Person or an Organization.');
        }
    }
}
