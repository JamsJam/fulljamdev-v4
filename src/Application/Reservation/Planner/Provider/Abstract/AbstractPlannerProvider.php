<?php

namespace App\Application\Reservation\Planner\Provider\Abstract;

use App\Repository\Reservation\PlanningRepository;

abstract class AbstractPlannerProvider
{
    public function __construct(protected readonly PlanningRepository $planningRepository)
    {
    }
}
