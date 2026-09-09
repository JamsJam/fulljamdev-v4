<?php

namespace App\Application\Blog\Category\Service;

use App\Entity\Blog\Category;
use App\Repository\Blog\CategoryRepository;
use App\Service\SluggerService;
use Doctrine\ORM\EntityManagerInterface;

final readonly class ResolveCategoryService
{
    private const MAX_SLUG_LENGTH = 140;

    public function __construct(
        private CategoryRepository $categories,
        private SluggerService $slugger,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function resolve(?string $name): ?Category
    {
        $name = null === $name ? '' : trim($name);
        if ('' === $name) {
            return null;
        }

        $category = $this->categories->findOneByNameCaseInsensitive($name);
        if (null !== $category) {
            return $category;
        }

        $base = $this->slugger->slugify($name, self::MAX_SLUG_LENGTH);
        if ('' === $base) {
            $base = 'categorie';
        }

        $slug = $base;
        $suffix = 2;
        while (null !== $this->categories->findOneBy(['slug' => $slug])) {
            $ending = '-'.$suffix++;
            $slug = substr($base, 0, self::MAX_SLUG_LENGTH - strlen($ending)).$ending;
        }

        $category = (new Category())->setName($name)->setSlug($slug);
        $this->entityManager->persist($category);

        return $category;
    }
}
