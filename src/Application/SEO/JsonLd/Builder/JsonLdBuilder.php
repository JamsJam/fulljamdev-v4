<?php

namespace App\Application\SEO\JsonLd\Builder;

use App\Application\SEO\JsonLd\Dto\PageContext;
use App\Application\SEO\JsonLd\Registry\PageDefinitionRegistry;

final readonly class JsonLdBuilder
{
    public function __construct(
        private PageDefinitionRegistry $definitions,
        private BreadcrumbBuilder $breadcrumb,
    ) {
    }

    /** @return array<string, mixed>|null */
    public function build(PageContext $context): ?array
    {
        if (!$context->indexable || '' === trim($context->title)) {
            return null;
        }

        $nodes = $this->definitions->get($context->type)->build($context);
        $breadcrumb = $this->breadcrumb->build($context);
        if (null !== $breadcrumb) {
            $nodes[] = $breadcrumb;
        }

        return [] === $nodes ? null : ['@context' => 'https://schema.org', '@graph' => $nodes];
    }

    /** Safe inside an HTML script element: user content cannot close the tag. */
    public function serialize(PageContext $context): ?string
    {
        $document = $this->build($context);

        return null === $document ? null : json_encode(
            $document,
            JSON_THROW_ON_ERROR | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
        );
    }
}
