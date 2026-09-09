<?php

namespace App\Application\Reservation\Appointment\Provider;

use App\Application\Reservation\Appointment\Provider\Abstract\AbstractAppointmentProvider;
use App\Entity\Reservation\Appointment;

final readonly class AppointmentsToProcessProvider extends AbstractAppointmentProvider
{
    /** @return Appointment[] */
    public function provide(\DateTimeImmutable $date): array
    {
        return $this->repository->findToProcess($date);
    }
}
