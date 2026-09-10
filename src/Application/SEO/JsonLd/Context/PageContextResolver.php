<?php

namespace App\Application\SEO\JsonLd\Context;

use App\Application\SEO\JsonLd\Dto\PageContext;
use App\Application\SEO\JsonLd\Interface\PageContextProviderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

final class PageContextResolver
{
    /** @var array<string, PageContextProviderInterface> */
    private array $providers = [];

    /** @param iterable<PageContextProviderInterface> $providers */
    public function __construct(#[AutowireIterator('app.json_ld.context_provider')] iterable $providers)
    {
        foreach ($providers as $provider) {
            foreach ($provider->routes() as $route) {
                if (isset($this->providers[$route])) {
                    throw new \LogicException(sprintf('JSON-LD route "%s" is registered twice.', $route));
                }
                $this->providers[$route] = $provider;
            }
        }
    }

    /** @param array<string, mixed> $view */
    public function resolve(string $route, array $view): ?PageContext
    {
        return isset($this->providers[$route]) ? $this->providers[$route]->provide($route, $view) : null;
    }
}
