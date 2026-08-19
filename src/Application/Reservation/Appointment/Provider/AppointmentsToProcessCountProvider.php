<?php

namespace App\Application\Reservation\Appointment\Provider;

use App\Application\Reservation\Appointment\Provider\Abstract\AbstractAppointmentProvider;

final readonly class AppointmentsToProcessCountProvider extends AbstractAppointmentProvider
{
    public function provide(\DateTimeImmutable $date): int
    {
        return $this->repository->countToProcess($date);
    }
}
