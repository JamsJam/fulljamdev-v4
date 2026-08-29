<?php

namespace App\Application\Blog\Article\Persister;

use App\Entity\Content\Article;
use Doctrine\ORM\EntityManagerInterface;

final readonly class ArticlePersister
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function persist(Article $article): void
    {
        $this->entityManager->persist($article);
        $this->entityManager->flush();
    }

    public function remove(Article $article): void
    {
        $this->entityManager->remove($article);
        $this->entityManager->flush();
    }
}
