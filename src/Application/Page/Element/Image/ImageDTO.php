<?php

namespace App\Application\Page\Element\Image;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class ImageDTO
{
    public ImageSource $source = ImageSource::URL;
    public ?string $mediaId = null;
    #[Ignore]
    #[Assert\Image(maxSize: '5M')]
    public ?UploadedFile $file = null;
    #[Assert\Url(requireTld: true)]
    public ?string $url = null;
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $alt = '';
    #[Assert\Length(max: 255)]
    public ?string $title = null;

    #[Assert\Callback]
    public function validateSource(ExecutionContextInterface $context): void
    {
        if (ImageSource::MEDIA === $this->source && null === $this->file && (null === $this->mediaId || '' === trim($this->mediaId))) {
            $context->buildViolation('Sélectionnez une image à envoyer.')->atPath('file')->addViolation();
        }
        if (ImageSource::URL === $this->source && (null === $this->url || '' === trim($this->url))) {
            $context->buildViolation('Renseignez l’URL de l’image.')->atPath('url')->addViolation();
        }
    }
}
