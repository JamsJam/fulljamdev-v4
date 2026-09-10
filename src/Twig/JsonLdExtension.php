<?php

namespace App\Twig;

use App\Application\SEO\JsonLd\Builder\JsonLdBuilder;
use App\Application\SEO\JsonLd\Context\PageContextResolver;
use App\Application\SEO\JsonLd\Dto\PageContext;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class JsonLdExtension extends AbstractExtension
{
    public function __construct(
        private readonly JsonLdBuilder $builder,
        private readonly PageContextResolver $contexts,
        private readonly RequestStack $requests,
    ) {
    }

    public function getFunctions(): array
    {
        return [new TwigFunction('page_json_ld', $this->render(...), ['needs_context' => true])];
    }

    /** @param array<string, mixed> $view */
    public function render(array $view): ?string
    {
        $request = $this->requests->getCurrentRequest();
        if (null === $request || !$request->isMethod('GET') || $request->attributes->has('exception') || $request->headers->has('Turbo-Frame')) {
            return null;
        }

        // An explicit context allows any new public page to opt in, independently
        // of routes, entities, controllers and the block-based PageBuilder.
        if (array_key_exists('json_ld_context', $view)) {
            $context = $view['json_ld_context'];
            if (null !== $context && !$context instanceof PageContext) {
                throw new \InvalidArgumentException('json_ld_context must be a PageContext or null.');
            }
        } else {
            $context = $this->contexts->resolve((string) $request->attributes->get('_route'), $view);
        }

        return null === $context ? null : $this->builder->serialize($context);
    }
}
