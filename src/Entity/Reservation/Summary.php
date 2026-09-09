<?php

namespace App\Entity\Reservation;

use App\Repository\Reservation\SummaryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SummaryRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Summary
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'summary')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Appointment $appointment = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'Le contenu du compte rendu est obligatoire.')]
    private string $content = '';

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $internalNotes = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $transcription = null;

    #[ORM\Column(length: 2048, nullable: true)]
    #[Assert\Url(requireTld: true, message: 'Le lien de l’enregistrement doit être une URL valide.')]
    private ?string $recordingLink = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAppointment(): ?Appointment
    {
        return $this->appointment;
    }

    public function setAppointment(Appointment $appointment): static
    {
        $this->appointment = $appointment;

        if ($appointment->getSummary() !== $this) {
            $appointment->setSummary($this);
        }

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getInternalNotes(): ?string
    {
        return $this->internalNotes;
    }

    public function setInternalNotes(?string $internalNotes): static
    {
        $this->internalNotes = $internalNotes;

        return $this;
    }

    public function getTranscription(): ?string
    {
        return $this->transcription;
    }

    public function setTranscription(?string $transcription): static
    {
        $this->transcription = $transcription;

        return $this;
    }

    public function getRecordingLink(): ?string
    {
        return $this->recordingLink;
    }

    public function setRecordingLink(?string $recordingLink): static
    {
        $this->recordingLink = $recordingLink;

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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    #[ORM\PrePersist]
    public function setTimestamps(): void
    {
        $now = new \DateTimeImmutable();
        $this->createdAt ??= $now;
        $this->updatedAt ??= $now;
    }

    #[ORM\PreUpdate]
    public function updateTimestamp(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}
