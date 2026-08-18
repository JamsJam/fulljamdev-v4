<?php

namespace App\Application\Reservation\Unavailability\Provider\Interface;

use App\Entity\Reservation\Unavailability;

interface UnavailabilityProviderInterface
{
    /**
     * @return Unavailability[]
     */
    public function provide(): array;
}
