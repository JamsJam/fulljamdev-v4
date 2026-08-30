<?php

namespace App\Application\Blog\Category\Factory;

use App\Application\Blog\Category\Dto\CategoryDto;
use App\Entity\Blog\Category;

final class CategoryFactory
{
    public function fromEntity(Category $category): CategoryDto
    {
        $dto = new CategoryDto();
        $dto->name = $category->getName();
        $dto->slug = $category->getSlug();
        $dto->description = $category->getDescription();

        return $dto;
    }

    public function create(CategoryDto $dto, ?Category $category = null): Category
    {
        return ($category ?? new Category())->setName($dto->name)->setSlug($dto->slug)->setDescription($dto->description);
    }
}
