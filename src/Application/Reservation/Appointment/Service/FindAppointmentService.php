<?php

namespace App\Application\Reservation\Appointment\Service;

use App\Application\Reservation\Appointment\Provider\AppointmentByIdProvider;
use App\Entity\Reservation\Appointment;

final readonly class FindAppointmentService
{
    public function __construct(private AppointmentByIdProvider $appointmentProvider)
    {
    }

    public function find(int $id): ?Appointment
    {
        return $this->appointmentProvider->provide(id: $id);
    }
}
