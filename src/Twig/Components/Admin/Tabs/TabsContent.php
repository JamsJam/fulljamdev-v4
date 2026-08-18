<?php

namespace App\Twig\Components\Admin\Tabs;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Admin:TabsContent', template: 'components/admin/tab/TabsContent.html.twig')]
final class TabsContent
{
    public string $id;

    public string $labelledby;

    public string $value = '';

    public bool $active = false;

    public bool $managed = true;
}
