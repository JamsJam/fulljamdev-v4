<?php

namespace App\Application\Blog\Article\Asset;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

final readonly class ArticleCoverImageUploader
{
    public function __construct(
        private string $targetDirectory,
        private SluggerInterface $slugger,
    ) {
    }

    public function upload(UploadedFile $file): string
    {
        if (!is_dir($this->targetDirectory) && !mkdir($this->targetDirectory, 0775, true) && !is_dir($this->targetDirectory)) {
            throw new FileException(sprintf('Le dossier d’upload « %s » ne peut pas être créé.', $this->targetDirectory));
        }

        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeName = strtolower((string) $this->slugger->slug($originalName));
        $extension = $file->guessExtension() ?: $file->getClientOriginalExtension();
        $filename = sprintf('%s-%s.%s', $safeName ?: 'cover', bin2hex(random_bytes(6)), $extension);
        $file->move($this->targetDirectory, $filename);

        return '/uploads/blog/'.$filename;
    }
}
