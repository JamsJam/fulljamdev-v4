<?php

namespace App\Application\Experience\Provider;

use App\Entity\Content\Experience;
use App\Repository\Content\ExperienceRepository;

final readonly class ExperienceProvider
{
    public function __construct(private ExperienceRepository $repository)
    {
    }

    /** @return list<Experience> */
    public function provideAll(): array
    {
        return $this->repository->findBy([], ['beginAt' => 'DESC', 'id' => 'DESC']);
    }

    /** @return list<Experience> */
    public function provideTimeline(): array
    {
        return $this->repository->findVisibleTimeline();
    }

    public function provideOne(int $id): ?Experience
    {
        return $this->repository->find($id);
    }
}
