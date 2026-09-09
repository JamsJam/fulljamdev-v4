<?php

namespace App\Application\Blog\Category\Service;

use App\Application\Blog\Category\Provider\CategoryProvider;
use App\Entity\Blog\Category;

final readonly class GetCategoriesService
{
    public function __construct(private CategoryProvider $provider)
    {
    }

    /** @return list<Category> */
    public function get(): array
    {
        return $this->provider->provideAll();
    }
}
