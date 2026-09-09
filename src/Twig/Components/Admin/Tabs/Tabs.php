<?php

namespace App\Twig\Components\Admin\Tabs;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Admin:Tabs', template: 'components/admin/tab/Tabs.html.twig')]
final class Tabs
{
    public string $defaultValue = '';

    public bool $navigation = false;
}
