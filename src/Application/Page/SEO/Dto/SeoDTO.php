<?php

namespace App\Application\Page\SEO\Dto;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Attribute\Ignore;
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
    public bool $profilePage = false;

    #[Assert\Length(max: 70)]
    public ?string $socialTitle = null;

    #[Assert\Length(max: 200)]
    public ?string $socialDescription = null;

    #[Assert\Length(max: 255)]
    public ?string $socialImagePath = null;

    #[Ignore]
    #[Assert\Image(maxSize: '5M', mimeTypes: ['image/jpeg', 'image/png', 'image/webp'])]
    public ?UploadedFile $socialImageFile = null;

    /** @var list<BreadcrumbLinkDTO> */
    #[Assert\Valid]
    public array $breadcrumbParents = [];
}
