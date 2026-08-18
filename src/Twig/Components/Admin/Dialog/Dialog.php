<?php

namespace App\Twig\Components\Admin\Dialog;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Admin:Dialog', template: 'components/admin/dialog/Dialog.html.twig')]
final class Dialog
{
    public string $title = '';
    public ?string $description = null;
}
