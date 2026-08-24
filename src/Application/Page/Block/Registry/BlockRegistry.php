<?php

namespace App\Application\Page\Block\Registry;

use App\Application\Page\Block\Interface\BlockDefinitionInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

final class BlockRegistry
{
    /** @var array<string, BlockDefinitionInterface> */
    private array $definitions = [];

    /** @param iterable<BlockDefinitionInterface> $definitions */
    public function __construct(#[AutowireIterator('app.page_block')] iterable $definitions)
    {
        foreach ($definitions as $definition) {
            if (isset($this->definitions[$definition->type()])) {
                throw new \LogicException(sprintf('Le type de bloc « %s » est déclaré plusieurs fois.', $definition->type()));
            }
            $this->definitions[$definition->type()] = $definition;
        }

        ksort($this->definitions);
    }

    public function get(string $type): BlockDefinitionInterface
    {
        return $this->definitions[$type] ?? throw new \InvalidArgumentException(sprintf('Le type de bloc « %s » est inconnu.', $type));
    }

    /** @return array<string, BlockDefinitionInterface> */
    public function all(): array
    {
        return $this->definitions;
    }

    /** @return array<string, BlockDefinitionInterface> */
    public function byCategory(string $category): array
    {
        return array_filter($this->definitions, static fn (BlockDefinitionInterface $definition): bool => $definition->category() === $category);
    }

    /** @return array<string, array<string, BlockDefinitionInterface>> */
    public function grouped(): array
    {
        $groups = [];
        foreach ($this->definitions as $type => $definition) {
            $groups[$definition->category()][$type] = $definition;
        }

        ksort($groups);

        return $groups;
    }
}
