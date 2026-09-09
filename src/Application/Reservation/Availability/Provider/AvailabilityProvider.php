<?php

namespace App\Application\Reservation\Availability\Provider;

use App\Application\Reservation\Availability\Provider\Abstract\AbstractAvailabilityProvider;
use App\Application\Reservation\Availability\Provider\Interface\AvailabilityProviderInterface;

final class AvailabilityProvider extends AbstractAvailabilityProvider implements AvailabilityProviderInterface
{
    public function provide(): array
    {
        return $this->availabilityRepository->findAll();
    }
}
