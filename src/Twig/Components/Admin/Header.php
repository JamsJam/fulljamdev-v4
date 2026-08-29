<?php

namespace App\Twig\Components\Admin;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Admin:Header', template: 'components/admin/Header.html.twig')]
final class Header
{
    /** @var list<array{label: string, route: string}> */
    public array $breadcrumb = [];

    public string $userName = 'Administrateur';

    public function getInitials(): string
    {
        $words = preg_split('/\s+/', trim($this->userName)) ?: [];
        $initials = '';

        foreach (array_slice($words, 0, 2) as $word) {
            $initials .= mb_strtoupper(mb_substr($word, 0, 1));
        }

        return $initials ?: 'A';
    }
}
