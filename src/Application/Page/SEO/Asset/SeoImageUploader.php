<?php

namespace App\Application\Page\SEO\Asset;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

final readonly class SeoImageUploader
{
    public function __construct(private string $targetDirectory, private SluggerInterface $slugger)
    {
    }

    public function upload(UploadedFile $file): string
    {
        if (!is_dir($this->targetDirectory) && !mkdir($this->targetDirectory, 0775, true) && !is_dir($this->targetDirectory)) {
            throw new FileException(sprintf('Le dossier d’upload « %s » ne peut pas être créé.', $this->targetDirectory));
        }

        $name = strtolower((string) $this->slugger->slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)));
        $extension = $file->guessExtension() ?: $file->getClientOriginalExtension();
        $filename = sprintf('social-%s-%s.%s', $name ?: 'page', bin2hex(random_bytes(6)), $extension);
        $file->move($this->targetDirectory, $filename);

        return '/uploads/pages/'.$filename;
    }
}
