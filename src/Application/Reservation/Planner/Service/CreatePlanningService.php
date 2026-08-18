<?php

namespace App\Application\Reservation\Planner\Service;

use App\Application\Reservation\Planner\Dto\PlanningDto;
use App\Application\Reservation\Planner\Factory\PlanningFactory;
use App\Application\Reservation\Planner\Persister\PlanningPersister;
use App\Entity\Reservation\Planning;

final class CreatePlanningService
{
    public function __construct(
        private readonly PlanningFactory $planningFactory,
        private readonly PlanningPersister $planningPersister,
    ) {
    }

    public function create(PlanningDto $dto): Planning
    {
        $planning = $this->planningFactory->create($dto);
        $this->planningPersister->persist($planning);

        return $planning;
    }
}
