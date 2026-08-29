<?php

namespace App\Application\Blog\Category\Service;

use App\Application\Blog\Category\Persister\CategoryPersister;
use App\Entity\Content\Category;

final readonly class DeleteCategoryService
{
    public function __construct(private CategoryPersister $persister)
    {
    }

    public function delete(Category $category): void
    {
        $this->persister->remove($category);
    }
}
