<?php

namespace App\Application\Blog\Article\Asset;

use App\Entity\Blog\Article;
use App\Entity\Blog\ArticleMedia;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final readonly class ArticleMediaUploader
{
    private const IMAGE_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/gif',
    ];

    private const VIDEO_MIME_TYPES = [
        'video/mp4',
        'video/webm',
        'video/ogg',
    ];

    private const MAX_IMAGE_SIZE = 5 * 1024 * 1024;
    private const MAX_VIDEO_SIZE = 50 * 1024 * 1024;

    public function __construct(private string $targetDirectory)
    {
    }

    public function upload(UploadedFile $file, ?Article $article = null): ArticleMedia
    {
        $mimeType = $file->getMimeType() ?? '';
        $mediaType = $this->resolveMediaType($mimeType);
        $size = $file->getSize();

        if ($size > $this->maxSizeFor($mediaType)) {
            throw new FileException(sprintf('Le fichier %s dépasse la taille maximale autorisée.', $mediaType));
        }

        $extension = $file->guessExtension() ?: $file->getClientOriginalExtension();
        $technicalName = $this->createTechnicalName($mediaType, $extension);
        $directory = $this->createTargetDirectory($mediaType);

        $file->move($directory, $technicalName);

        return (new ArticleMedia())
            ->setArticle($article)
            ->setType($mediaType)
            ->setTechnicalName($technicalName)
            ->setDisplayName($file->getClientOriginalName())
            ->setPath(sprintf('/uploads/blog/media/%s/%s', $mediaType, $technicalName))
            ->setMimeType($mimeType)
            ->setSize($size);
    }

    private function resolveMediaType(string $mimeType): string
    {
        return match (true) {
            in_array($mimeType, self::IMAGE_MIME_TYPES, true) => 'image',
            in_array($mimeType, self::VIDEO_MIME_TYPES, true) => 'video',
            default => throw new FileException('Ce type de fichier n’est pas autorisé.'),
        };
    }

    private function maxSizeFor(string $mediaType): int
    {
        return 'image' === $mediaType ? self::MAX_IMAGE_SIZE : self::MAX_VIDEO_SIZE;
    }

    private function createTechnicalName(string $mediaType, string $extension): string
    {
        return sprintf('%s-%s.%s', $mediaType, bin2hex(random_bytes(16)), strtolower($extension));
    }

    private function createTargetDirectory(string $mediaType): string
    {
        $directory = $this->targetDirectory.'/'.$mediaType;
        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new FileException(sprintf('Le dossier d’upload « %s » ne peut pas être créé.', $directory));
        }

        return $directory;
    }
}
