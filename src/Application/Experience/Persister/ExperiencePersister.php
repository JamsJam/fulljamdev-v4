<?php

namespace App\Application\Experience\Persister;

use App\Entity\Experience\Experience;
use Doctrine\ORM\EntityManagerInterface;

final readonly class ExperiencePersister
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function persist(Experience $experience): void
    {
        $this->entityManager->persist($experience);
        $this->entityManager->flush();
    }

    public function remove(Experience $experience): void
    {
        $this->entityManager->remove($experience);
        $this->entityManager->flush();
    }
}
