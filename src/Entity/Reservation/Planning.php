<?php

namespace App\Entity\Reservation;

use App\Repository\Reservation\PlanningRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PlanningRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(
    fields: ['color'],
    message: 'Cette couleur est déjà utilisée par un autre planning.',
)]
#[UniqueEntity(fields: ['slug'], message: 'Ce lien de réservation existe déjà.')]
class Planning
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 70)]
    #[Assert\NotNull(message: 'Le nom du planning est obligatoire.')]
    #[Assert\NotBlank(message: 'Le nom du planning est obligatoire.')]
    #[Assert\Length(
        min: 1,
        max: 70,
        minMessage: 'Le nom du planning doit contenir au moins {{ limit }} caractère.',
        maxMessage: 'Le nom du planning ne peut pas dépasser {{ limit }} caractères.',
    )]
    private ?string $title = null;

    #[ORM\Column(length: 92, unique: true)]
    #[Assert\NotBlank(message: 'Le slug du planning est obligatoire.')]
    #[Assert\Length(max: 92)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Assert\NotNull(message: 'La durée du rendez-vous est obligatoire.')]
    #[Assert\Range(
        min: 5,
        max: 60,
        notInRangeMessage: 'La durée du rendez-vous doit être comprise entre {{ min }} et {{ max }} minutes.',
    )]
    private ?int $duration = null;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Assert\NotNull(message: 'Le délai entre deux rendez-vous est obligatoire.')]
    #[Assert\Range(
        min: 10,
        max: 40,
        notInRangeMessage: 'Le délai entre deux rendez-vous doit être compris entre {{ min }} et {{ max }} minutes.',
    )]
    private ?int $gap = null;

    #[ORM\Column(length: 7, unique: true)]
    #[Assert\NotBlank(message: 'La couleur du planning est obligatoire.')]
    #[Assert\CssColor(
        formats: Assert\CssColor::HEX_LONG,
        message: 'La couleur doit être au format hexadécimal #RRGGBB.',
    )]
    private string $color = '#6750A4';

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $editedAt = null;

    #[ORM\Column(options: ['default' => false])]
    private bool $isActive = false;

    /**
     * @var Collection<int, Availability>
     */
    #[ORM\OneToMany(targetEntity: Availability::class, mappedBy: 'planning', orphanRemoval: true)]
    private Collection $availabilities;

    /**
     * @var Collection<int, Appointment>
     */
    #[ORM\OneToMany(targetEntity: Appointment::class, mappedBy: 'planning', orphanRemoval: true)]
    private Collection $appointments;

    public function __construct()
    {
        $this->availabilities = new ArrayCollection();
        $this->appointments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getGap(): ?int
    {
        return $this->gap;
    }

    public function setGap(int $gap): static
    {
        $this->gap = $gap;

        return $this;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function setColor(string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getEditedAt(): ?\DateTimeImmutable
    {
        return $this->editedAt;
    }

    public function setEditedAt(\DateTimeImmutable $editedAt): static
    {
        $this->editedAt = $editedAt;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @return Collection<int, Availability>
     */
    public function getAvailabilities(): Collection
    {
        return $this->availabilities;
    }

    public function addAvailability(Availability $availability): static
    {
        if (!$this->availabilities->contains($availability)) {
            $this->availabilities->add($availability);
            $availability->setPlanning($this);
        }

        return $this;
    }

    public function removeAvailability(Availability $availability): static
    {
        if ($this->availabilities->removeElement($availability)) {
            if ($availability->getPlanning() === $this) {
                $availability->setPlanning(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Appointment>
     */
    public function getAppointments(): Collection
    {
        return $this->appointments;
    }

    public function addAppointment(Appointment $appointment): static
    {
        if (!$this->appointments->contains($appointment)) {
            $this->appointments->add($appointment);
            $appointment->setPlanning($this);
        }

        return $this;
    }

    public function removeAppointment(Appointment $appointment): static
    {
        if ($this->appointments->removeElement($appointment)) {
            if ($appointment->getPlanning() === $this) {
                $appointment->setPlanning(null);
            }
        }

        return $this;
    }

    #[ORM\PrePersist]
    public function setTimestamps(): void
    {
        $now = new \DateTimeImmutable();

        $this->createdAt ??= $now;
        $this->editedAt ??= $now;
    }

    #[ORM\PreUpdate]
    public function updateEditedAt(): void
    {
        $this->editedAt = new \DateTimeImmutable();
    }
}
