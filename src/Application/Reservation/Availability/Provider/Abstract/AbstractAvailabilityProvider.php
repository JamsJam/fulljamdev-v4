<?php

namespace App\Application\Reservation\Availability\Provider\Abstract;

use App\Repository\Reservation\AvailabilityRepository;

abstract class AbstractAvailabilityProvider
{
    public function __construct(protected readonly AvailabilityRepository $availabilityRepository)
    {
    }
}
