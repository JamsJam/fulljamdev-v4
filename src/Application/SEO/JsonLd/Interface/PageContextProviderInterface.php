<?php

namespace App\Application\SEO\JsonLd\Interface;

use App\Application\SEO\JsonLd\Dto\PageContext;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/** Adapts existing public view data; the document builder never sees these sources. */
#[AutoconfigureTag('app.json_ld.context_provider')]
interface PageContextProviderInterface
{
    /** @return list<string> */
    public function routes(): array;

    /** @param array<string, mixed> $view */
    public function provide(string $route, array $view): ?PageContext;
}
