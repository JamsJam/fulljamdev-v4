<?php

namespace App\Application\Reservation\Planner\Provider;

use App\Application\Reservation\Planner\Provider\Abstract\AbstractPlannerProvider;
use App\Application\Reservation\Planner\Provider\Interface\PlannerProviderInterface;
use App\Entity\Reservation\Planning;

final class PlannerProvider extends AbstractPlannerProvider implements PlannerProviderInterface
{
    public function provide(): array
    {
        return $this->planningRepository->findBy([], ['title' => 'ASC']);
    }

    public function provideOne(int $id): ?Planning
    {
        return $this->planningRepository->find($id);
    }

    public function provideOneBySlug(string $slug): ?Planning
    {
        return $this->planningRepository->findOneBy(['slug' => $slug]);
    }

    public function colorExists(string $color): bool
    {
        return null !== $this->planningRepository->findOneBy(['color' => $color]);
    }
}
