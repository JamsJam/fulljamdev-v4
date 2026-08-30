<?php

namespace App\Application\Project\Dto;

use App\Entity\Project\Technology;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class ProjectDto
{
    #[Assert\NotBlank, Assert\Length(max: 180)] public string $title = '';
    #[Assert\Length(max: 1000)] public ?string $excerpt = null;
    #[Assert\NotBlank, Assert\Length(max: 50000)] public string $content = '';
    /** @var list<UploadedFile> */
    #[Assert\All([new Assert\Image(maxSize: '5M', mimeTypes: ['image/jpeg', 'image/png', 'image/webp'])])]
    public array $imageFiles = [];
    /** @var list<Technology> */
    #[Assert\All([new Assert\Type(Technology::class)])]
    public array $technologies = [];
    #[Assert\Url(requireTld: true)] public ?string $websiteUrl = null;
    #[Assert\Url(requireTld: true)] public ?string $repositoryUrl = null;
    public bool $isFeatured = false;
    #[Assert\Choice(choices: ['draft', 'published'])] public string $status = 'draft';
    public ?\DateTimeImmutable $publishedAt = null;
}
