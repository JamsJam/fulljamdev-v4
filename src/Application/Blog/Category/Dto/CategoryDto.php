<?php

namespace App\Application\Blog\Category\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class CategoryDto
{
    #[Assert\NotBlank, Assert\Length(max: 120)] public string $name = '';
    #[Assert\NotBlank, Assert\Regex(pattern: '/^[a-z0-9-]+$/'), Assert\Length(max: 140)] public string $slug = '';
    public ?string $description = null;
}
