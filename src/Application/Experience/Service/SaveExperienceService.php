<?php

namespace App\Application\Experience\Service;

use App\Application\Experience\Dto\ExperienceDto;
use App\Application\Experience\Factory\ExperienceFactory;
use App\Application\Experience\Persister\ExperiencePersister;
use App\Entity\Content\Experience;

final readonly class SaveExperienceService
{
    public function __construct(private ExperienceFactory $factory, private ExperiencePersister $persister)
    {
    }

    public function save(ExperienceDto $dto, ?Experience $experience = null): Experience
    {
        $experience = $this->factory->create($dto, $experience);
        $this->persister->persist($experience);

        return $experience;
    }
}
