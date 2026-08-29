<?php

namespace App\Application\Settings\General\Asset;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final readonly class SiteAssetUploader
{
    public function __construct(private string $targetDirectory)
    {
    }

    public function upload(UploadedFile $file, string $name): string
    {
        if (!is_dir($this->targetDirectory) && !mkdir($this->targetDirectory, 0775, true) && !is_dir($this->targetDirectory)) {
            throw new FileException(sprintf('Le dossier d’upload « %s » ne peut pas être créé.', $this->targetDirectory));
        }

        $extension = $file->guessExtension() ?: $file->getClientOriginalExtension();
        $filename = sprintf('%s-%s.%s', $name, bin2hex(random_bytes(6)), $extension);
        $file->move($this->targetDirectory, $filename);

        return '/uploads/settings/'.$filename;
    }
}
