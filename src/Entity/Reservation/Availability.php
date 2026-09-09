<?php

namespace App\Entity\Reservation;

use App\Repository\Reservation\AvailabilityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: AvailabilityRepository::class)]
class Availability
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'availabilities')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Planning $planning = null;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Assert\NotNull(message: 'Le jour est obligatoire.')]
    #[Assert\NotBlank(message: 'Le jour ne peut pas être vide.')]
    #[Assert\Range(
        min: 1,
        max: 7,
        notInRangeMessage: 'Le jour doit être compris entre {{ min }} et {{ max }}.',
    )]
    private ?int $dow = null;

    #[ORM\Column(type: Types::TIME_IMMUTABLE)]
    #[Assert\NotNull(message: 'L’heure de début est obligatoire.')]
    private ?\DateTimeImmutable $startHour = null;

    #[ORM\Column(type: Types::TIME_IMMUTABLE)]
    #[Assert\NotNull(message: 'L’heure de fin est obligatoire.')]
    private ?\DateTimeImmutable $endHour = null;

    #[Assert\Callback]
    public function validateMinimumDuration(ExecutionContextInterface $context): void
    {
        if (null === $this->startHour || null === $this->endHour) {
            return;
        }

        if ($this->endHour < $this->startHour->modify('+90 minutes')) {
            $context
                ->buildViolation('L’heure de fin doit être au moins 1 h 30 après l’heure de début.')
                ->atPath('endHour')
                ->addViolation();
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlanning(): ?Planning
    {
        return $this->planning;
    }

    public function setPlanning(?Planning $planning): static
    {
        $this->planning = $planning;

        return $this;
    }

    public function getDow(): ?int
    {
        return $this->dow;
    }

    public function setDow(int $dow): static
    {
        $this->dow = $dow;

        return $this;
    }

    public function getStartHour(): ?\DateTimeImmutable
    {
        return $this->startHour;
    }

    public function setStartHour(\DateTimeImmutable $startHour): static
    {
        $this->startHour = $startHour;

        return $this;
    }

    public function getEndHour(): ?\DateTimeImmutable
    {
        return $this->endHour;
    }

    public function setEndHour(\DateTimeImmutable $endHour): static
    {
        $this->endHour = $endHour;

        return $this;
    }
}
