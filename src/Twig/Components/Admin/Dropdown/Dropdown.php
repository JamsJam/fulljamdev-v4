<?php

namespace App\Twig\Components\Admin\Dropdown;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Admin:Dropdown', template: 'components/admin/dropdown/Dropdown.html.twig')]
final class Dropdown
{
    public string $label = 'Options';
    public ?string $icon = null;
    public string $variant = 'secondary';
    public bool $onlyIcon = false;
    public string $alignment = 'end';

    /**
     * @var array<int, array{label: string, href?: string|null, turboFrame?: string|null, copyText?: string|null}>
     */
    public array $items = [];
}
