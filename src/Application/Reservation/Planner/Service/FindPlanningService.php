<?php

namespace App\Application\Reservation\Planner\Service;

use App\Application\Reservation\Planner\Provider\Interface\PlannerProviderInterface;
use App\Entity\Reservation\Planning;

final class FindPlanningService
{
    public function __construct(private readonly PlannerProviderInterface $plannerProvider)
    {
    }

    public function find(int $id): ?Planning
    {
        return $this->plannerProvider->provideOne($id);
    }

    public function findBySlug(string $slug): ?Planning
    {
        return $this->plannerProvider->provideOneBySlug($slug);
    }
}
