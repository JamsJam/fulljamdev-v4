<?php

namespace App\Application\LinkTree\Dto;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

final class LinkTreeDto
{
    #[Assert\Length(max: 180)] public ?string $name = null;
    #[Assert\Length(max: 180)] public ?string $company = null;
    #[Assert\Image(maxSize: '5M', mimeTypes: ['image/jpeg', 'image/png', 'image/webp'])]
    public ?UploadedFile $profilePictureFile = null;
}
