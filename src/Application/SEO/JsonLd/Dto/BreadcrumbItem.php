<?php

namespace App\Application\SEO\JsonLd\Dto;

final readonly class BreadcrumbItem
{
    public function __construct(public string $name, public string $url)
    {
    }
}
