<?php

namespace App\Application\Blog\Category\Provider;

use App\Entity\Blog\Category;
use App\Repository\Blog\CategoryRepository;

final readonly class CategoryProvider
{
    public function __construct(private CategoryRepository $repository)
    {
    }

    /** @return list<Category> */
    public function provideAll(): array
    {
        return $this->repository->findBy([], ['name' => 'ASC']);
    }

    public function provideOne(int $id): ?Category
    {
        return $this->repository->find($id);
    }

    public function slugExists(string $slug, ?int $exceptId = null): bool
    {
        $category = $this->repository->findOneBy(['slug' => $slug]);

        return null !== $category && $category->getId() !== $exceptId;
    }
}
