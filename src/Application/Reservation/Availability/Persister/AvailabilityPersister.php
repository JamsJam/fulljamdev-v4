<?php

namespace App\Application\Reservation\Availability\Persister;

use App\Entity\Reservation\Availability;
use App\Entity\Reservation\Planning;
use Doctrine\ORM\EntityManagerInterface;

final class AvailabilityPersister
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * @param Availability[] $availabilities
     */
    public function replaceForPlanning(Planning $planning, array $availabilities): void
    {
        foreach ($planning->getAvailabilities()->toArray() as $availability) {
            $planning->removeAvailability($availability);
            $this->entityManager->remove($availability);
        }

        foreach ($availabilities as $availability) {
            $planning->addAvailability($availability);
            $this->entityManager->persist($availability);
        }

        $this->entityManager->flush();
    }
}
