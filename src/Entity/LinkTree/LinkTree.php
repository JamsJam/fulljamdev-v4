<?php

namespace App\Entity\LinkTree;

use App\Repository\LinkTree\LinkTreeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LinkTreeRepository::class)]
#[ORM\Table(name: 'link_tree')]
class LinkTree
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 2048, nullable: true)]
    private ?string $profilePicture = null;

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $company = null;

    /** @var Collection<int, Link> */
    #[ORM\OneToMany(mappedBy: 'linkTree', targetEntity: Link::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['id' => 'ASC'])]
    private Collection $links;

    public function __construct()
    {
        $this->links = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    public function setProfilePicture(?string $profilePicture): static
    {
        $this->profilePicture = $profilePicture;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): static
    {
        $this->company = $company;

        return $this;
    }

    /** @return Collection<int, Link> */
    public function getLinks(): Collection
    {
        return $this->links;
    }

    public function addLink(Link $link): static
    {
        if (!$this->links->contains($link)) {
            $this->links->add($link);
            $link->setLinkTree($this);
        }

        return $this;
    }

    public function removeLink(Link $link): static
    {
        if ($this->links->removeElement($link) && $link->getLinkTree() === $this) {
            $link->setLinkTree(null);
        }

        return $this;
    }
}
