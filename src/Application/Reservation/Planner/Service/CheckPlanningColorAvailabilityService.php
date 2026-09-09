<?php

namespace App\Application\Reservation\Planner\Service;

use App\Application\Reservation\Planner\Provider\Interface\PlannerProviderInterface;

final readonly class CheckPlanningColorAvailabilityService
{
    public function __construct(private PlannerProviderInterface $plannerProvider)
    {
    }

    public function isAvailable(string $color): bool
    {
        return !$this->plannerProvider->colorExists($color);
    }
}
