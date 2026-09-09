<?php

namespace App\Application\Page\Data\Registry;

use App\Application\Page\Data\Dto\NumberValueDTO;
use App\Application\Page\Data\Enum\ValueSource;
use App\Application\Page\Data\Interface\DynamicValueProviderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

final class DynamicValueRegistry
{
    /** @var array<string, DynamicValueProviderInterface> */
    private array $providers = [];

    /** @param iterable<DynamicValueProviderInterface> $providers */
    public function __construct(#[AutowireIterator('app.page_dynamic_value')] iterable $providers)
    {
        foreach ($providers as $provider) {
            if (isset($this->providers[$provider->key()])) {
                throw new \LogicException(sprintf('La source dynamique « %s » est déclarée plusieurs fois.', $provider->key()));
            }
            $this->providers[$provider->key()] = $provider;
        }
    }

    public function resolveNumber(NumberValueDTO $value): ?int
    {
        if (ValueSource::STATIC === $value->source) {
            return $value->value;
        }

        $provider = $this->providers[$value->sourceKey ?? ''] ?? throw new \InvalidArgumentException(sprintf('La source dynamique « %s » est inconnue.', $value->sourceKey));
        $resolved = $provider->resolve();

        return is_numeric($resolved) ? (int) $resolved : null;
    }
}
