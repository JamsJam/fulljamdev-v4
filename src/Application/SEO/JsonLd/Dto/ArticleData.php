<?php

namespace App\Application\SEO\JsonLd\Dto;

final readonly class ArticleData
{
    public function __construct(
        public ?\DateTimeImmutable $publishedAt = null,
        public ?\DateTimeImmutable $modifiedAt = null,
        public ?IdentityData $author = null,
    ) {
    }
}
