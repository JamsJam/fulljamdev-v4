<?php

namespace App\Repository\QrVisitCard;

use App\Entity\QrVisitCard\QrVisitCard;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<QrVisitCard> */
final class QrVisitCardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QrVisitCard::class);
    }
}
