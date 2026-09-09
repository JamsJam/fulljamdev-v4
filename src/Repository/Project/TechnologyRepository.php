<?php

namespace App\Repository\Project;

use App\Entity\Project\Technology;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Technology> */
final class TechnologyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Technology::class);
    }

    public function findOneByName(string $name): ?Technology
    {
        return $this->findOneBy(['name' => $name]);
    }
}
