<?php

namespace App\Application\Blog\Article\Dto;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

final class ArticleDto
{
    #[Assert\Length(max: 50)] public ?string $title = null;
    #[Assert\Regex(pattern: '/^[a-z0-9-]+$/'), Assert\Length(max: 200)] public ?string $slug = null;
    #[Assert\Length(max: 120)] public ?string $categoryName = null;
    #[Assert\Length(max: 160)] public ?string $summary = null;
    public ?string $content = null;
    #[Assert\Length(max: 255)] public ?string $coverImage = null;
    #[Assert\Image(maxSize: '5M', mimeTypes: ['image/jpeg', 'image/png', 'image/webp'])]
    public ?UploadedFile $coverImageFile = null;
    public ?\DateTimeImmutable $publishedAt = null;
}
