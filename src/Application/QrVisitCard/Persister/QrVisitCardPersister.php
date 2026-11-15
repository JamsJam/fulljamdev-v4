<?php

namespace App\Application\QrVisitCard\Persister;

use App\Entity\QrVisitCard\QrVisitCard;
use Doctrine\ORM\EntityManagerInterface;

final readonly class QrVisitCardPersister
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function persist(QrVisitCard $qrVisitCard): void
    {
        $this->entityManager->persist($qrVisitCard);
        $this->entityManager->flush();
    }
}
