<?php

namespace App\Twig\Components\Admin;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class Menu
{
    /** @var array<int, array{name: string, route: string}> */
    public array $menu = [
        [
            'name' => 'Dashboard',
            'route' => 'app_admin_dashboard',
        ],
        [
            'name' => 'Projets',
            'route' => 'app_admin_project_index',
        ],
        [
            'name' => 'Contenus',
            'route' => 'app_admin_content',
        ],
        [
            'name' => 'Commentaire',
            'route' => 'app_admin_dashboard',
        ],
        [
            'name' => 'Catégories',
            'route' => 'app_admin_dashboard',
        ],
        [
            'name' => 'Tags',
            'route' => 'app_admin_dashboard',
        ],
        [
            'name' => 'Statistiques',
            'route' => 'app_admin_dashboard',
        ],
        [
            'name' => 'Paramètres',
            'route' => 'app_admin_dashboard',
        ],
    ];
}
