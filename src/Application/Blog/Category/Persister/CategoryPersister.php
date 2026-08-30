<?php

namespace App\Application\Blog\Category\Persister;

use App\Entity\Blog\Category;
use Doctrine\ORM\EntityManagerInterface;

final readonly class CategoryPersister
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function persist(Category $category): void
    {
        $this->entityManager->persist($category);
        $this->entityManager->flush();
    }

    public function remove(Category $category): void
    {
        $this->entityManager->remove($category);
        $this->entityManager->flush();
    }
}
