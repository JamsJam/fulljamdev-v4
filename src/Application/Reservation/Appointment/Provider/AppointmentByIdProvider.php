<?php

namespace App\Application\Reservation\Appointment\Provider;

use App\Application\Reservation\Appointment\Provider\Abstract\AbstractAppointmentProvider;
use App\Entity\Reservation\Appointment;

final readonly class AppointmentByIdProvider extends AbstractAppointmentProvider
{
    public function provide(int $id): ?Appointment
    {
        return $this->repository->find($id);
    }
}
