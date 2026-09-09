<?php

namespace App\Application\Reservation\Planner\Service;

use App\Application\Reservation\Planner\Provider\Interface\PlannerProviderInterface;
use App\Entity\Reservation\Planning;

final class GetPlanningsService
{
    public function __construct(private readonly PlannerProviderInterface $plannerProvider)
    {
    }

    /**
     * @return Planning[]
     */
    public function get(): array
    {
        return $this->plannerProvider->provide();
    }
}
