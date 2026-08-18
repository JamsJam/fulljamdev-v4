<?php

namespace App\Application\Reservation\Appointment\Provider;

use App\Application\Reservation\Appointment\Provider\Abstract\AbstractAppointmentProvider;
use App\Application\Reservation\Appointment\Provider\Interface\AppointmentProviderInterface;
use App\Entity\Reservation\Appointment;

final readonly class AppointmentByIdProvider extends AbstractAppointmentProvider implements AppointmentProviderInterface
{
    /**
     * Utiliser exclusivement l'argument nommé `id`.
     * Cet argument est obligatoire.
     */
    public function provide(
        ?int $id = null,
        ?\DateTimeImmutable $startAt = null,
        ?\DateTimeImmutable $endAt = null,
        array $statuses = [],
        ?\DateTimeImmutable $date = null,
    ): ?Appointment {
        if (null === $id) {
            throw new \InvalidArgumentException("L'identifiant du rendez-vous est obligatoire.");
        }

        return $this->repository->find($id);
    }
}
