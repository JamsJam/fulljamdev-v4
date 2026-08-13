<?php

namespace App\Service\Breadcrumb;

final class BreadcrumbService
{
    public function __construct(
        private readonly BreadcrumbBuilder $builder,
    ) {
    }

    /**
     * @return list<array{label: string, route: string}>
     */
    public function getBreadcrumb(string $route): array
    {
        if ('' === trim($route) || '_' === $route || str_starts_with($route, '_') || str_ends_with($route, '_')) {
            throw new \InvalidArgumentException('The route cannot be empty.');
        }

        return $this->builder->build(explode('_', $route));
    }
}
