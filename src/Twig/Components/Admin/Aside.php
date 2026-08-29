<?php

namespace App\Twig\Components\Admin;

use App\Application\Reservation\Appointment\Provider\AppointmentsToProcessCountProvider;
use Symfony\Component\Clock\ClockInterface;
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
     *         activePrefixes?: list<string>,
     *         children?: list<array{label: string, route: string, badge?: string|int, activePrefixes?: list<string>}>
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
                    'icon' => 'material-symbols:calendar-month-outline-rounded',
                    'children' => [
                        [
                            'label' => 'Rendez-vous',
                            'route' => 'app_dashboard_reservation',
                            'activePrefixes' => ['app_dashboard_reservation_appointment_'],
                        ],
                        [
                            'label' => 'Calendrier',
                            'route' => 'app_dashboard_reservation_calendar',
                        ],
                        [
                            'label' => 'À traiter',
                            'route' => 'app_dashboard_reservation_processing',
                        ],
                        [
                            'label' => 'Plannings',
                            'route' => 'app_dashboard_reservation_plannings',
                            'activePrefixes' => ['app_dashboard_reservation_planning_'],
                        ],
                    ],
                ],
            ],
        ],
        [
            'label' => 'Contenus',
            'items' => [
                [
                    'label' => 'Projets',
                    'route' => 'app_dashboard_project',
                    'icon' => 'grommet-icons:projects',
                    'activePrefixes' => ['app_dashboard_project_'],
                ],
                [
                    'label' => 'Blog',
                    'route' => 'app_dashboard_blog',
                    'icon' => 'iconoir:post',
                    'activePrefixes' => ['app_dashboard_blog_'],
                ],
                [
                    'label' => 'CV',
                    'route' => 'app_dashboard_cv',
                    'icon' => 'material-symbols:work-outline-rounded',
                    'activePrefixes' => ['app_dashboard_cv_'],
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
        AppointmentsToProcessCountProvider $appointmentsToProcessCountProvider,
        ClockInterface $clock,
    ) {
        $this->groups[0]['items'][1]['children'][2]['badge'] = $appointmentsToProcessCountProvider->provide(
            date: \DateTimeImmutable::createFromInterface($clock->now()),
        );
    }

    /** @param list<string> $activePrefixes */
    public function isActive(string $route, array $activePrefixes = []): bool
    {
        $currentRoute = (string) $this->requestStack->getCurrentRequest()?->attributes->get('_route');
        if ($route === $currentRoute) {
            return true;
        }

        foreach ($activePrefixes as $prefix) {
            if (str_starts_with($currentRoute, $prefix)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param list<array{route: string, activePrefixes?: list<string>}> $items
     */
    public function hasActiveChild(array $items): bool
    {
        foreach ($items as $item) {
            if ($this->isActive($item['route'], $item['activePrefixes'] ?? [])) {
                return true;
            }
        }

        return false;
    }
}
