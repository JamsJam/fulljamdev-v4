<?php

namespace App\Application\Page\SEO\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class SeoDTO
{
    #[Assert\Length(max: 70)]
    public ?string $title = null;
    #[Assert\Length(max: 170)]
    public ?string $description = null;
    #[Assert\Url(protocols: ['http', 'https'], relativeProtocol: true, requireTld: true)]
    public ?string $canonicalUrl = null;
    public bool $noIndex = false;
}
