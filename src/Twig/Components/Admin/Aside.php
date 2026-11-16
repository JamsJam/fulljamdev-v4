<?php

namespace App\Twig\Components\Admin;

use App\Application\Reservation\Appointment\Provider\AppointmentsToProcessCountProvider;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Admin:Aside', template: 'components/admin/Aside.html.twig')]
final class Aside
{
    public string $brand = 'Fulljamdev';

    /**
     * @var list<array{
     *     label?: string,
     *     items: list<array{
     *         label: string,
     *         route?: string,
     *         routeParameters?: array<string, string|int>,
     *         icon?: string,
     *         badge?: string|int,
     *         logout?: bool,
     *         activePrefixes?: list<string>,
     *         children?: list<array{
     *             label: string,
     *             route?: string,
     *             routeParameters?: array<string, string|int>,
     *             icon?: string,
     *             badge?: string|int,
     *             activePrefixes?: list<string>
     *         }>
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
                    'label' => 'Déconnexion',
                    'route' => 'app_logout',
                    'icon' => 'bx:log-out',
                    'logout' => true,
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
                    'icon' => 'pepicons-pencil:cv',
                    'activePrefixes' => ['app_dashboard_cv_'],
                ],
                [
                    'label' => 'Pages',
                    'route' => 'app_dashboard_page',
                    'icon' => 'iconoir:post',
                    'activePrefixes' => ['app_dashboard_page_'],
                ],
            ],
        ],
        [
            'label' => 'CRM',
            'items' => [
                [
                    'label' => 'Réservations',
                    'icon' => 'material-symbols:calendar-month-outline-rounded',
                    'children' => [
                        [
                            'label' => 'Rendez-vous',
                            'route' => 'app_dashboard_reservation',
                            // 'activePrefixes' => ['app_dashboard_reservation_appointment_'],
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
                [
                    'label' => 'LinkTree',
                    'route' => 'app_dashboard_linktree',
                    'icon' => 'material-symbols:link-rounded',
                    'activePrefixes' => ['app_dashboard_linktree_'],
                ],
            ],
        ],
        [
            'label' => 'Configuration',
            'items' => [
                [
                    'label' => 'Paramètres',
                    'route' => 'app_dashboard_settings',
                    'routeParameters' => ['section' => 'general'],
                    'icon' => 'ic:round-settings',
                ],
                [
                    'label' => 'Mon compte',
                    'route' => 'app_dashboard_settings',
                    'routeParameters' => ['section' => 'account'],
                    'icon' => 'ic:round-people',
                ],
                [
                    'label' => 'Legal',
                    'children' => [
                        [
                            'label' => 'Politique de confidentialité',
                            'route' => 'app_dashboard_legal',
                            'icon' => 'qlementine-icons:resume-16',
                        ],
                        [
                            'label' => 'Mentions légales',
                            'route' => 'app_dashboard_legal',
                            'icon' => 'qlementine-icons:resume-16',
                        ], [
                            'label' => 'Conditions générales d’utilisation',
                            'route' => 'app_dashboard_legal',
                            'icon' => 'qlementine-icons:resume-16',
                        ],
                    ],
                ],
            ],
        ],
    ];

    public function __construct(
        private readonly RequestStack $requestStack,
        AppointmentsToProcessCountProvider $appointmentsToProcessCountProvider,
        ClockInterface $clock,
    ) {
        $badge = $appointmentsToProcessCountProvider->provide(
            date: \DateTimeImmutable::createFromInterface($clock->now()),
        );

        foreach ($this->groups as $groupIndex => $group) {
            foreach ($group['items'] as $itemIndex => $item) {
                foreach ($item['children'] ?? [] as $childIndex => $child) {
                    if ('app_dashboard_reservation_processing' === ($child['route'] ?? null)) {
                        $this->groups[$groupIndex]['items'][$itemIndex]['children'][$childIndex]['badge'] = $badge;
                    }
                }
            }
        }
    }

    /**
     * @param list<string>              $activePrefixes
     * @param array<string, string|int> $routeParameters
     */
    public function isActive(string $route, array $activePrefixes = [], array $routeParameters = []): bool
    {
        $request = $this->requestStack->getCurrentRequest();
        $currentRoute = (string) $request?->attributes->get('_route');
        $parametersMatch = true;
        foreach ($routeParameters as $name => $value) {
            if ((string) $request?->attributes->get((string) $name) !== (string) $value) {
                $parametersMatch = false;
                break;
            }
        }

        if ($route === $currentRoute && $parametersMatch) {
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
     * @param list<array{route?: string, activePrefixes?: list<string>}> $items
     */
    public function hasActiveChild(array $items): bool
    {
        foreach ($items as $item) {
            if (isset($item['route']) && $this->isActive($item['route'], $item['activePrefixes'] ?? [])) {
                return true;
            }
        }

        return false;
    }
}
