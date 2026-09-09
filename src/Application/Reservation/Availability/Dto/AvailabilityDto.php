<?php

namespace App\Application\Reservation\Availability\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class AvailabilityDto
{
    public function __construct(
        #[Assert\NotNull(message: 'Le jour est obligatoire.')]
        #[Assert\Range(
            min: 1,
            max: 7,
            notInRangeMessage: 'Le jour doit être compris entre {{ min }} et {{ max }}.',
        )]
        public ?int $dow = null,

        #[Assert\When(
            expression: 'this.isAvailable',
            constraints: [
                new Assert\NotNull(message: 'L’heure de début est obligatoire.'),
            ],
        )]
        public ?\DateTimeImmutable $startHour = null,

        #[Assert\When(
            expression: 'this.isAvailable',
            constraints: [
                new Assert\NotNull(message: 'L’heure de fin est obligatoire.'),
            ],
        )]
        public ?\DateTimeImmutable $endHour = null,

        public bool $isAvailable = false,
    ) {
    }

    #[Assert\Callback]
    public function validateMinimumDuration(ExecutionContextInterface $context): void
    {
        if (!$this->isAvailable || null === $this->startHour || null === $this->endHour) {
            return;
        }

        if ($this->endHour < $this->startHour->modify('+90 minutes')) {
            $context
                ->buildViolation('L’heure de fin doit être au moins 1 h 30 après l’heure de début.')
                ->atPath('endHour')
                ->addViolation();
        }
    }
}
