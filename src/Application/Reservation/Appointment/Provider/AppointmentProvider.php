<?php

namespace App\Application\Reservation\Appointment\Provider;

use App\Application\Reservation\Appointment\Provider\Abstract\AbstractAppointmentProvider;
use App\Application\Reservation\Appointment\Provider\Interface\AppointmentProviderInterface;
use App\Entity\Reservation\Appointment;

final readonly class AppointmentProvider extends AbstractAppointmentProvider implements AppointmentProviderInterface
{
    /**
     * Utiliser exclusivement les arguments nommés `startAt` et `endAt`.
     * Ces deux arguments sont obligatoires.
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
        if (null === $startAt || null === $endAt) {
            throw new \InvalidArgumentException('Les dates de début et de fin sont obligatoires.');
        }

        return $this->repository->findStartingBetween($startAt, $endAt);
    }
}
