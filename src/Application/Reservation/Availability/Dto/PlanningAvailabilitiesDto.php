<?php

namespace App\Application\Reservation\Availability\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class PlanningAvailabilitiesDto
{
    /**
     * @param AvailabilityDto[] $availabilities
     */
    public function __construct(
        #[Assert\Valid]
        public array $availabilities = [],
    ) {
    }
}
