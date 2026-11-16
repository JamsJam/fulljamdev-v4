<?php

namespace App\Entity\LinkTree;

use App\Repository\LinkTree\LinkRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LinkRepository::class)]
#[ORM\Table(name: 'link_tree_link')]
class Link
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'links')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?LinkTree $linkTree = null;

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $label = null;

    #[ORM\Column(length: 2048, nullable: true)]
    #[Assert\Url(requireTld: true)]
    private ?string $href = null;

    #[ORM\Column(options: ['default' => false])]
    private bool $isOnline = false;

    #[ORM\Column(options: ['default' => false])]
    private bool $isPrefered = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLinkTree(): ?LinkTree
    {
        return $this->linkTree;
    }

    public function setLinkTree(?LinkTree $linkTree): static
    {
        $this->linkTree = $linkTree;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getHref(): ?string
    {
        return $this->href;
    }

    public function setHref(?string $href): static
    {
        $this->href = $href;

        return $this;
    }

    public function isOnline(): bool
    {
        return $this->isOnline;
    }

    public function setIsOnline(bool $isOnline): static
    {
        $this->isOnline = $isOnline;
        if (!$isOnline) {
            $this->isPrefered = false;
        }

        return $this;
    }

    public function isPrefered(): bool
    {
        return $this->isPrefered;
    }

    public function setIsPrefered(bool $isPrefered): static
    {
        $this->isPrefered = $isPrefered && $this->isOnline;

        return $this;
    }
}
