<?php

namespace App\Application\Reservation\Unavailability\Provider;

use App\Application\Reservation\Unavailability\Provider\Abstract\AbstractUnavailabilityProvider;
use App\Application\Reservation\Unavailability\Provider\Interface\UnavailabilityProviderInterface;

final class UnavailabilityProvider extends AbstractUnavailabilityProvider implements UnavailabilityProviderInterface
{
    public function provide(): array
    {
        return $this->unavailabilityRepository->findAll();
    }
}
