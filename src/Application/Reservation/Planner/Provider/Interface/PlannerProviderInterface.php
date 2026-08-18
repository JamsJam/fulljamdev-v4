<?php

namespace App\Application\Reservation\Planner\Provider\Interface;

use App\Entity\Reservation\Planning;

interface PlannerProviderInterface
{
    /**
     * @return Planning[]
     */
    public function provide(): array;

    public function provideOne(int $id): ?Planning;

    public function provideOneBySlug(string $slug): ?Planning;

    public function colorExists(string $color): bool;
}
