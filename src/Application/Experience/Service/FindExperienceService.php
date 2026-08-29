<?php

namespace App\Application\Experience\Service;

use App\Application\Experience\Provider\ExperienceProvider;
use App\Entity\Content\Experience;

final readonly class FindExperienceService
{
    public function __construct(private ExperienceProvider $provider)
    {
    }

    public function find(int $id): ?Experience
    {
        return $this->provider->provideOne($id);
    }
}
