<?php

namespace App\Application\QrVisitCard\Provider;

use App\Entity\QrVisitCard\QrVisitCard;
use App\Repository\QrVisitCard\QrVisitCardRepository;

final readonly class QrVisitCardProvider
{
    public function __construct(private QrVisitCardRepository $repository)
    {
    }

    public function provide(): ?QrVisitCard
    {
        return $this->repository->findOneBy([], ['id' => 'ASC']);
    }
}
