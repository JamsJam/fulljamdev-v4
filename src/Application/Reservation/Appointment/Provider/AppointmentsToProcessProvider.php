<?php

namespace App\Application\Reservation\Appointment\Provider;

use App\Application\Reservation\Appointment\Provider\Abstract\AbstractAppointmentProvider;
use App\Application\Reservation\Appointment\Provider\Interface\AppointmentProviderInterface;
use App\Entity\Reservation\Appointment;

final readonly class AppointmentsToProcessProvider extends AbstractAppointmentProvider implements AppointmentProviderInterface
{
    /**
     * Utiliser exclusivement l'argument nommé `date`.
     * Cet argument est obligatoire et sert de date de référence.
     *
     * @return Appointment[]
     */
    public function provide(
        ?int $id = null,
        ?\DateTimeImmutable $startAt = null,
        ?\DateTimeImmutable $endAt = null,
        array $statuses = [],
        ?\DateTimeImmutable $date = null,
    ): array {
        if (null === $date) {
            throw new \InvalidArgumentException('La date de référence est obligatoire.');
        }

        return $this->repository->findToProcess($date);
    }
}
