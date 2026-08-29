<?php

namespace App\Application\Project\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class ProjectDto
{
    #[Assert\NotBlank, Assert\Length(max: 180)] public string $title = '';
    #[Assert\NotBlank, Assert\Regex(pattern: '/^[a-z0-9-]+$/'), Assert\Length(max: 200)] public string $slug = '';
    #[Assert\Length(max: 320)] public ?string $excerpt = null;
    #[Assert\NotBlank] public string $content = '';
    #[Assert\Length(max: 255)] public ?string $featuredImage = null;
    /** @var list<string> */ public array $technologies = [];
    #[Assert\Url(requireTld: true)] public ?string $websiteUrl = null;
    #[Assert\Url(requireTld: true)] public ?string $repositoryUrl = null;
    public bool $isFeatured = false;
    #[Assert\Choice(choices: ['draft', 'published'])] public string $status = 'draft';
    public ?\DateTimeImmutable $publishedAt = null;
}
