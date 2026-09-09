<?php

namespace App\Application\Reservation\Planner\Factory;

use App\Application\Reservation\Planner\Dto\PlanningDto;
use App\Application\Reservation\Planner\Service\PlanningSlugGenerator;
use App\Entity\Reservation\Planning;
use App\Service\HtmlSanitizerService;

final readonly class PlanningFactory
{
    public function __construct(
        private HtmlSanitizerService $htmlSanitizer,
        private PlanningSlugGenerator $slugGenerator,
    ) {
    }

    public function create(PlanningDto $dto): Planning
    {
        return (new Planning())
            ->setTitle((string) $dto->title)
            ->setSlug($this->slugGenerator->generate((string) $dto->title))
            ->setDescription($this->sanitizeDescription($dto->description))
            ->setDuration((int) $dto->duration)
            ->setGap((int) $dto->gap)
            ->setColor($dto->color)
            ->setIsActive(false);
    }

    private function sanitizeDescription(?string $description): ?string
    {
        if (null === $description || '' === trim($description)) {
            return null;
        }

        return $this->htmlSanitizer->sanitize($description);
    }
}
