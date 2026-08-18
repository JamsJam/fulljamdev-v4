<?php

namespace App\Twig\Components\Admin\Dropdown;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Admin:Dropdown:Item', template: 'components/admin/dropdown/Item.html.twig')]
final class Item
{
    public string $label = '';
    public ?string $href = null;
    public ?string $turboFrame = null;
    public ?string $copyText = null;
}
