<?php

namespace App\Entity\Blog;

use App\Repository\Blog\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 56, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(length: 160, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $slug = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $editedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $publishedAt = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $status = null;

    #[ORM\ManyToOne(inversedBy: 'post')]
    private ?BlogCategory $category = null;

    /**
     * @var Collection<int, BlogTags>
     */
    #[ORM\ManyToMany(targetEntity: BlogTags::class, mappedBy: 'post')]
    private Collection $tags;

    #[ORM\ManyToOne(inversedBy: 'post')]
    private ?BlogAuthor $author = null;

    /**
     * @var Collection<int, Author>
     */
    #[ORM\ManyToMany(targetEntity: BlogAuthor::class, mappedBy: 'collaborated')]
    private Collection $collaborator;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->collaborator = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): static
    {
        $this->slug = $slug;

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

    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeImmutable $publishedAt): static
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCategory(): ?BlogCategory
    {
        return $this->category;
    }

    public function setCategory(?BlogCategory $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, BlogTags>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(BlogTags $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
            $tag->addPost($this);
        }

        return $this;
    }

    public function removeTag(BlogTags $tag): static
    {
        if ($this->tags->removeElement($tag)) {
            $tag->removePost($this);
        }

        return $this;
    }

    public function getAuthor(): ?BlogAuthor
    {
        return $this->author;
    }

    public function setAuthor(?BlogAuthor $author): static
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection<int, Author>
     */
    public function getCollaborator(): Collection
    {
        return $this->collaborator;
    }

    public function addCollaborator(BlogAuthor $collaborator): static
    {
        if (!$this->collaborator->contains($collaborator)) {
            $this->collaborator->add($collaborator);
            $collaborator->addCollaborated($this);
        }

        return $this;
    }

    public function removeCollaborator(BlogAuthor $collaborator): static
    {
        if ($this->collaborator->removeElement($collaborator)) {
            $collaborator->removeCollaborated($this);
        }

        return $this;
    }
}
