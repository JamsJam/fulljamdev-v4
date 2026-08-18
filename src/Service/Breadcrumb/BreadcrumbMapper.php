<?php

namespace App\Service\Breadcrumb;

final class BreadcrumbMapper
{
    /**
     * @var array<string, array{label: string, route?: string}>
     */
    private const ELEMENTS = [
        'app' => [
            'label' => 'Fulljamdev',
            'route' => 'app_home',
        ],
        'dashboard' => [
            'label' => 'Dashboard',
        ],
        'reservation' => [
            'label' => 'Réservations',
        ],
        'calendar' => [
            'label' => 'Calendrier',
        ],
        'processing' => [
            'label' => 'À traiter',
        ],
        'plannings' => [
            'label' => 'Plannings',
        ],
        'settings' => [
            'label' => 'Paramètres',
        ],
    ];

    /**
     * @return array{label: string, route?: string}
     */
    public function get(string $element): array
    {
        return self::ELEMENTS[$element] ?? throw new \InvalidArgumentException(sprintf('The breadcrumb element "%s" is not configured.', $element));
    }
}
