<?php

namespace App\Application\Blog\Category\Service;

use App\Application\Blog\Category\Dto\CategoryDto;
use App\Application\Blog\Category\Factory\CategoryFactory;
use App\Application\Blog\Category\Persister\CategoryPersister;
use App\Entity\Blog\Category;

final readonly class SaveCategoryService
{
    public function __construct(private CategoryFactory $factory, private CategoryPersister $persister)
    {
    }

    public function save(CategoryDto $dto, ?Category $category = null): Category
    {
        $category = $this->factory->create($dto, $category);
        $this->persister->persist($category);

        return $category;
    }
}
