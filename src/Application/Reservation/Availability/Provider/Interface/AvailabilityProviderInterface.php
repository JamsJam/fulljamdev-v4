<?php

namespace App\Application\Reservation\Availability\Provider\Interface;

use App\Entity\Reservation\Availability;

interface AvailabilityProviderInterface
{
    /**
     * @return Availability[]
     */
    public function provide(): array;
}
