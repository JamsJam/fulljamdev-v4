<?php

namespace App\Application\Reservation\Planner\Service;

use App\Service\SluggerService;
use App\Service\UuidGeneratorService;

final readonly class PlanningSlugGenerator
{
    private const TITLE_MAX_LENGTH = 70;

    public function __construct(
        private SluggerService $slugger,
        private UuidGeneratorService $uuidGenerator,
    ) {
    }

    public function generate(string $title): string
    {
        $uuid = str_replace('-', '', $this->uuidGenerator->v4());
        $titleSlug = $this->slugger->slugify($title, self::TITLE_MAX_LENGTH);

        return sprintf(
            '%s-%s-%s',
            substr($uuid, 0, 10),
            '' === $titleSlug ? 'planning' : $titleSlug,
            substr($uuid, -10),
        );
    }
}
