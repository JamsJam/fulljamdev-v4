<?php

namespace App\Twig\Components\Admin;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class Breadcrumb
{
    /** @var list<array{label: string, route: string}> */
    public array $items = [];
}
