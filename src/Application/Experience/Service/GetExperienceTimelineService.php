<?php

namespace App\Application\Experience\Service;

use App\Application\Experience\Provider\ExperienceProvider;
use App\Entity\Content\Experience;

final readonly class GetExperienceTimelineService
{
    public function __construct(private ExperienceProvider $experiences)
    {
    }

    /** @return list<Experience> */
    public function get(): array
    {
        return $this->experiences->provideTimeline();
    }
}
