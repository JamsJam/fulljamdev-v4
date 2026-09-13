<?php

namespace App\Entity\Legal;

use App\Repository\Legal\LegalPageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LegalPageRepository::class)]
#[ORM\Table(name: 'content_legal_page')]
class LegalPage
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    private string $slug = '';

    #[ORM\Column(length: 160)]
    private string $title = '';

    #[ORM\Column(type: Types::TEXT)]
    private string $content = '';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = trim($slug);

        return $this;
    }

    public function setTitle(string $title): static
    {
        $this->title = trim($title);

        return $this;
    }

    public function setContent(string $content): static
    {
        $this->content = trim($content);

        return $this;
    }
}
