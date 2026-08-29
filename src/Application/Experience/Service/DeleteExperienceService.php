<?php

namespace App\Application\Experience\Service;

use App\Application\Experience\Persister\ExperiencePersister;
use App\Entity\Content\Experience;

final readonly class DeleteExperienceService
{
    public function __construct(private ExperiencePersister $persister)
    {
    }

    public function delete(Experience $experience): void
    {
        $this->persister->remove($experience);
    }
}
