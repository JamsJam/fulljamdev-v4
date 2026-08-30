<?php

namespace App\Repository\Experience;

use App\Entity\Experience\Experience;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Experience> */
final class ExperienceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Experience::class);
    }

    /** @return list<Experience> */
    public function findVisibleTimeline(): array
    {
        return $this->findBy(['isVisible' => true], ['beginAt' => 'DESC', 'id' => 'DESC']);
    }
}
