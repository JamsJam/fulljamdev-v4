<?php

namespace App\Application\SEO\JsonLd\Interface;

use App\Application\Page\Page\Dto\PageBlockDTO;
use App\Application\SEO\JsonLd\Dto\JsonLdContribution;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.json_ld.block_contributor')]
interface BlockJsonLdContributorInterface
{
    public function supports(PageBlockDTO $block): bool;

    /** $blockId is an absolute, page-scoped identifier supplied by the collector. */
    public function contribute(PageBlockDTO $block, string $blockId): JsonLdContribution;
}
