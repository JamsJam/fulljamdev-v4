<?php

namespace App\Application\Reservation\Appointment\Provider;

use App\Application\Reservation\Appointment\Enum\AppointmentStatus;
use App\Application\Reservation\Appointment\Provider\Abstract\AbstractAppointmentProvider;
use App\Entity\Reservation\Appointment;

final readonly class AppointmentsByPeriodAndStatusesProvider extends AbstractAppointmentProvider
{
    /**
     * @param list<AppointmentStatus> $statuses
     *
     * @return Appointment[]
     */
    public function provide(
        \DateTimeImmutable $startAt,
        \DateTimeImmutable $endAt,
        array $statuses,
    ): array {
        return $this->repository->findStartingBetweenByStatuses($startAt, $endAt, $statuses);
    }
}
