<?php

namespace App\Twig\Components\Admin\Tabs;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Admin:TabsList', template: 'components/admin/tab/TabsList.html.twig')]
final class TabsList
{
    public string $label = 'Onglets';
}
