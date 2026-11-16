<?php

namespace App\Application\LinkTree\Asset;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

final readonly class LinkTreePhotoUploader
{
    public function __construct(private string $targetDirectory, private SluggerInterface $slugger)
    {
    }

    public function upload(UploadedFile $file): string
    {
        if (!is_dir($this->targetDirectory) && !mkdir($this->targetDirectory, 0775, true) && !is_dir($this->targetDirectory)) {
            throw new FileException('Le dossier de photo LinkTree ne peut pas être créé.');
        }
        $filename = $this->slugger->slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)).'-'.bin2hex(random_bytes(6)).'.'.$file->guessExtension();
        $file->move($this->targetDirectory, $filename);

        return $filename;
    }
}
