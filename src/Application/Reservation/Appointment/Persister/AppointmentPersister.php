<?php

namespace App\Application\Reservation\Appointment\Persister;

use App\Entity\Reservation\Appointment;
use Doctrine\ORM\EntityManagerInterface;

final readonly class AppointmentPersister
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function persist(Appointment $appointment): void
    {
        $this->entityManager->persist($appointment);
    }
}
