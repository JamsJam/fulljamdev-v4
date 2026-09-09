<?php

namespace App\Twig\Components\Admin;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Admin:Breadcrumb', template: 'components/admin/Breadcrumb.html.twig')]
final class Breadcrumb
{
    /** @var list<array{label: string, route: string}> */
    public array $items = [];
}
