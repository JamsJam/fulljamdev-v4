<?php

namespace App\Application\Reservation\Planner\Service;

use App\Application\Reservation\Planner\Dto\PlanningDto;
use App\Entity\Reservation\Planning;
use App\Service\HtmlSanitizerService;
use Doctrine\ORM\EntityManagerInterface;

final readonly class UpdatePlanningService
{
    public function __construct(private EntityManagerInterface $entityManager, private HtmlSanitizerService $sanitizer)
    {
    }

    public function update(Planning $planning, PlanningDto $dto): void
    {
        $planning->setTitle((string) $dto->title)
            ->setDescription(null === $dto->description ? null : $this->sanitizer->sanitize($dto->description))
            ->setDuration((int) $dto->duration)
            ->setGap((int) $dto->gap)
            ->setColor($dto->color)
            ->setIsActive($dto->isActive)
            ->setIsOnline($dto->isOnline);
        // Preserve the public URL, availability ranges and existing appointments.
        $this->entityManager->flush();
    }
}
