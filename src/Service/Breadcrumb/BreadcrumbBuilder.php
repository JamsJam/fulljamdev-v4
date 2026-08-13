<?php

namespace App\Service\Breadcrumb;

final class BreadcrumbBuilder
{
    public function __construct(
        private readonly BreadcrumbMapper $mapper,
    ) {
    }

    /**
     * @param list<string> $elements
     *
     * @return list<array{label: string, route: string}>
     */
    public function build(array $elements): array
    {
        $breadcrumb = [];
        $routeParts = [];

        foreach ($elements as $element) {
            if ('' === $element) {
                throw new \InvalidArgumentException('A breadcrumb element cannot be empty.');
            }

            $routeParts[] = $element;
            $mappedElement = $this->mapper->get($element);

            $breadcrumb[] = [
                'label' => $mappedElement['label'],
                'route' => $mappedElement['route'] ?? implode('_', $routeParts),
            ];
        }

        return $breadcrumb;
    }
}
