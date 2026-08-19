<?php

namespace App\Application\Reservation\Appointment\Provider;

use App\Application\Reservation\Appointment\Enum\AppointmentStatus;
use App\Application\Reservation\Appointment\Provider\Abstract\AbstractAppointmentProvider;
use App\Entity\Reservation\Appointment;

final readonly class AppointmentsByStatusesProvider extends AbstractAppointmentProvider
{
    /**
     * @param list<AppointmentStatus> $statuses
     *
     * @return Appointment[]
     */
    public function provide(array $statuses): array
    {
        return $this->repository->findByStatuses($statuses);
    }
}
