<?php

namespace App\Application\SEO\JsonLd\Dto;

final readonly class WebsiteData
{
    public function __construct(public string $name, public string $url, public ?IdentityData $publisher = null)
    {
    }
}
