<?php

namespace App\Application\Experience\Service;

use App\Application\Experience\Provider\ExperienceProvider;
use App\Entity\Content\Experience;

final readonly class GetExperiencesService
{
    public function __construct(private ExperienceProvider $provider)
    {
    }

    /** @return list<Experience> */
    public function get(): array
    {
        return $this->provider->provideAll();
    }
}
