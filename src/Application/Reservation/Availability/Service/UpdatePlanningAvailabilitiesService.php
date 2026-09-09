<?php

namespace App\Application\Reservation\Availability\Service;

use App\Application\Reservation\Availability\Dto\AvailabilityDto;
use App\Application\Reservation\Availability\Factory\AvailabilityFactory;
use App\Application\Reservation\Availability\Persister\AvailabilityPersister;
use App\Entity\Reservation\Planning;

final class UpdatePlanningAvailabilitiesService
{
    public function __construct(
        private readonly AvailabilityFactory $availabilityFactory,
        private readonly AvailabilityPersister $availabilityPersister,
    ) {
    }

    /**
     * @param AvailabilityDto[] $dtos
     */
    public function update(Planning $planning, array $dtos): void
    {
        $availabilities = [];

        foreach ($dtos as $dto) {
            if (!$dto->isAvailable || null === $dto->dow || null === $dto->startHour || null === $dto->endHour) {
                continue;
            }

            $availabilities[] = $this->availabilityFactory->create($dto, $planning);
        }

        $this->availabilityPersister->replaceForPlanning($planning, $availabilities);
    }
}
