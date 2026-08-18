<?php

namespace App\Application\Reservation\Appointment\Provider\Abstract;

use App\Repository\Reservation\AppointmentRepository;

abstract readonly class AbstractAppointmentProvider
{
    public function __construct(
        protected AppointmentRepository $repository,
    ) {
    }
}
