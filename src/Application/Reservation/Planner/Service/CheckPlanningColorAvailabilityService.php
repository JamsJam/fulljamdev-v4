<?php

namespace App\Application\Reservation\Planner\Service;

use App\Application\Reservation\Planner\Provider\Interface\PlannerProviderInterface;
use App\Entity\Reservation\Planning;

final readonly class CheckPlanningColorAvailabilityService
{
    public function __construct(private PlannerProviderInterface $plannerProvider)
    {
    }

    public function isAvailable(string $color, ?Planning $planning = null): bool
    {
        if (null !== $planning && strtolower($planning->getColor()) === strtolower($color)) {
            return true;
        }

        return !$this->plannerProvider->colorExists($color);
    }
}
