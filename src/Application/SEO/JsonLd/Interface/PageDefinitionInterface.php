<?php

namespace App\Application\SEO\JsonLd\Interface;

use App\Application\SEO\JsonLd\Dto\PageContext;
use App\Application\SEO\JsonLd\Enum\PageType;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.json_ld.definition')]
interface PageDefinitionInterface
{
    /** @return list<PageType> */
    public function types(): array;

    /** @return list<array<string, mixed>> Graph nodes, without @context. */
    public function build(PageContext $context): array;
}
