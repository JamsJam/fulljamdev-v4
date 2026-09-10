<?php

namespace App\Application\SEO\JsonLd\Registry;

use App\Application\SEO\JsonLd\Enum\PageType;
use App\Application\SEO\JsonLd\Interface\PageDefinitionInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

final class PageDefinitionRegistry
{
    /** @var array<string, PageDefinitionInterface> */
    private array $definitions = [];

    /** @param iterable<PageDefinitionInterface> $definitions */
    public function __construct(#[AutowireIterator('app.json_ld.definition')] iterable $definitions)
    {
        foreach ($definitions as $definition) {
            foreach ($definition->types() as $type) {
                if (isset($this->definitions[$type->value])) {
                    throw new \LogicException(sprintf('JSON-LD page type "%s" is registered twice.', $type->value));
                }
                $this->definitions[$type->value] = $definition;
            }
        }
    }

    public function get(PageType $type): PageDefinitionInterface
    {
        return $this->definitions[$type->value] ?? throw new \InvalidArgumentException(sprintf('No JSON-LD definition for "%s".', $type->value));
    }
}
