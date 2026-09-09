<?php

namespace App\Application\Reservation\Appointment\Provider;

use App\Application\Reservation\Appointment\Provider\Abstract\AbstractAppointmentProvider;
use App\Entity\Reservation\Appointment;

final readonly class AppointmentProvider extends AbstractAppointmentProvider
{
    /** @return Appointment[] */
    public function provide(
        \DateTimeImmutable $startAt,
        \DateTimeImmutable $endAt,
    ): array {
        return $this->repository->findStartingBetween($startAt, $endAt);
    }
}
