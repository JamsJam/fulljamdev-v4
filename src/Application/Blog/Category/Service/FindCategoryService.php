<?php

namespace App\Application\Blog\Category\Service;

use App\Application\Blog\Category\Provider\CategoryProvider;
use App\Entity\Content\Category;

final readonly class FindCategoryService
{
    public function __construct(private CategoryProvider $provider)
    {
    }

    public function find(int $id): ?Category
    {
        return $this->provider->provideOne($id);
    }
}
