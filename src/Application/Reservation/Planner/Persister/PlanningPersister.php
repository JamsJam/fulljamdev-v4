<?php

namespace App\Application\Reservation\Planner\Persister;

use App\Entity\Reservation\Planning;
use Doctrine\ORM\EntityManagerInterface;

final class PlanningPersister
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function persist(Planning $planning): void
    {
        $this->entityManager->persist($planning);
        $this->entityManager->flush();
    }
}
