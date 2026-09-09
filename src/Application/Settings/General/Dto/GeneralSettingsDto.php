<?php

namespace App\Application\Settings\General\Dto;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Validator\Constraints as Assert;

final class GeneralSettingsDto
{
    #[Assert\NotBlank(message: 'Le titre du site est obligatoire.')]
    #[Assert\Length(max: 120)]
    public string $siteTitle = 'FullJam Dev';

    public ?string $logoPath = null;

    #[Ignore]
    #[Assert\Image(maxSize: '5M', mimeTypes: ['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'])]
    public ?UploadedFile $logoFile = null;

    public ?string $faviconSvgPath = null;

    #[Ignore]
    #[Assert\File(maxSize: '1M', mimeTypes: ['image/svg+xml'])]
    public ?UploadedFile $faviconSvgFile = null;

    public ?string $faviconIcoPath = null;

    #[Ignore]
    #[Assert\File(maxSize: '1M', mimeTypes: ['image/x-icon', 'image/vnd.microsoft.icon', 'application/octet-stream'])]
    public ?UploadedFile $faviconIcoFile = null;

    public ?string $appleTouchIconPath = null;

    #[Ignore]
    #[Assert\Image(maxSize: '1M', mimeTypes: ['image/png'])]
    public ?UploadedFile $appleTouchIconFile = null;

    #[Assert\NotBlank(message: 'Sélectionnez un fuseau horaire.')]
    #[Assert\Timezone(message: 'Ce fuseau horaire n’est pas valide.')]
    public string $timezone = 'Europe/Paris';

    #[Assert\NotNull(message: 'Sélectionnez une page d’accueil.')]
    #[Assert\Positive(message: 'La page d’accueil sélectionnée n’est pas valide.')]
    public ?int $homepagePageId = null;
}
