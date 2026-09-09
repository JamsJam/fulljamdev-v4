<?php

namespace App\Entity\Page;

use App\Repository\Page\PageBlockRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PageBlockRepository::class)]
#[ORM\Table(name: 'content_page_block')]
#[ORM\Index(name: 'page_block_position_idx', columns: ['page_id', 'position'])]
class PageBlock
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'blocks')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Page $page = null;

    #[ORM\Column(length: 100)]
    private string $type = '';

    #[ORM\Column]
    private int $position = 0;

    /** @var array<string, mixed> */
    #[ORM\Column(type: Types::JSON)]
    private array $data = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPage(): ?Page
    {
        return $this->page;
    }

    public function setPage(Page $page): static
    {
        $this->page = $page;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }

    /** @return array<string, mixed> */
    public function getData(): array
    {
        return $this->data;
    }

    /** @param array<string, mixed> $data */
    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }
}
