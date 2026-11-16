<?php

namespace App\Repository\LinkTree;

use App\Entity\LinkTree\LinkTree;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<LinkTree> */
final class LinkTreeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LinkTree::class);
    }
}
