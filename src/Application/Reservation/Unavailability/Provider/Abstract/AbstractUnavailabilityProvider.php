<?php

namespace App\Application\Reservation\Unavailability\Provider\Abstract;

use App\Repository\Reservation\UnavailabilityRepository;

abstract class AbstractUnavailabilityProvider
{
    public function __construct(protected readonly UnavailabilityRepository $unavailabilityRepository)
    {
    }
}
