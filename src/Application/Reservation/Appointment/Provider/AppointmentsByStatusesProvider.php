<?php

namespace App\Application\Reservation\Appointment\Provider;

use App\Application\Reservation\Appointment\Enum\AppointmentStatus;
use App\Application\Reservation\Appointment\Provider\Abstract\AbstractAppointmentProvider;
use App\Application\Reservation\Appointment\Provider\Interface\AppointmentProviderInterface;
use App\Entity\Reservation\Appointment;

final readonly class AppointmentsByStatusesProvider extends AbstractAppointmentProvider implements AppointmentProviderInterface
{
    /**
     * Utiliser exclusivement l'argument nommé `statuses`.
     *
     * @param list<AppointmentStatus> $statuses
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
        return $this->repository->findByStatuses($statuses);
    }
}
