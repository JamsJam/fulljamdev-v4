<?php

namespace App\Application\Blog\Article\Dto;

use App\Entity\Content\Category;
use Symfony\Component\Validator\Constraints as Assert;

final class ArticleDto
{
    #[Assert\NotBlank, Assert\Length(max: 180)] public string $title = '';
    #[Assert\NotBlank, Assert\Regex(pattern: '/^[a-z0-9-]+$/'), Assert\Length(max: 200)] public string $slug = '';
    public ?Category $category = null;
    #[Assert\Length(max: 320)] public ?string $excerpt = null;
    #[Assert\NotBlank] public string $content = '';
    #[Assert\Length(max: 255)] public ?string $featuredImage = null;
    #[Assert\Choice(choices: ['draft', 'published'])] public string $status = 'draft';
    public ?\DateTimeImmutable $publishedAt = null;
}
