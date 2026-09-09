<?php

namespace App\Application\Reservation\Availability\Factory;

use App\Application\Reservation\Availability\Dto\AvailabilityDto;
use App\Entity\Reservation\Availability;
use App\Entity\Reservation\Planning;

final class AvailabilityFactory
{
    public function create(AvailabilityDto $dto, Planning $planning): Availability
    {
        return (new Availability())
            ->setPlanning($planning)
            ->setDow((int) $dto->dow)
            ->setStartHour($dto->startHour ?? throw new \LogicException('L’heure de début est obligatoire.'))
            ->setEndHour($dto->endHour ?? throw new \LogicException('L’heure de fin est obligatoire.'));
    }
}
