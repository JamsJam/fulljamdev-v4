<?php

namespace App\Twig\Components\Admin;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class Aside
{
    public string $brand = 'Fulljamdev';

    /**
     * @var list<array{
     *     label?: string,
     *     items: list<array{
     *         label: string,
     *         route?: string,
     *         icon?: string,
     *         badge?: string|int,
     *         children?: list<array{label: string, route: string, badge?: string|int}>
     *     }>
     * }>
     */
    public array $groups = [
        [
            'items' => [
                [
                    'label' => 'Dashboard',
                    'route' => 'app_dashboard',
                    'icon' => 'grommet-icons:projects',
                ],
                [
                    'label' => 'Réservations',
                    'route' => 'app_dashboard_reservation',
                    'icon' => 'iconoir:post',
                ],
            ],
        ],
        [
            'label' => 'Contenus',
            'items' => [
                [
                    'label' => 'Gestion des contenus',
                    'icon' => 'iconoir:post',
                    'children' => [
                        ['label' => 'Projets', 'route' => 'app_dashboard'],
                        ['label' => 'Blog', 'route' => 'app_dashboard'],
                        ['label' => 'CV', 'route' => 'app_dashboard'],
                    ],
                ],
            ],
        ],
        [
            'label' => 'Configuration',
            'items' => [
                [
                    'label' => 'Paramètres',
                    'route' => 'app_dashboard_settings',
                    'icon' => 'ic:round-settings',
                ],
            ],
        ],
    ];

    public function __construct(
        private readonly RequestStack $requestStack,
    ) {
    }

    public function isActive(string $route): bool
    {
        return $route === $this->requestStack->getCurrentRequest()?->attributes->get('_route');
    }

    /**
     * @param list<array{route: string}> $items
     */
    public function hasActiveChild(array $items): bool
    {
        foreach ($items as $item) {
            if ($this->isActive($item['route'])) {
                return true;
            }
        }

        return false;
    }
}
