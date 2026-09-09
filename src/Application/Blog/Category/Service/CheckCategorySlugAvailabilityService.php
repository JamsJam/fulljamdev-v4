<?php

namespace App\Application\Blog\Category\Service;

use App\Application\Blog\Category\Provider\CategoryProvider;
use App\Entity\Blog\Category;

final readonly class CheckCategorySlugAvailabilityService
{
    public function __construct(private CategoryProvider $provider)
    {
    }

    public function isUsed(string $slug, ?Category $category = null): bool
    {
        return $this->provider->slugExists($slug, $category?->getId());
    }
}
