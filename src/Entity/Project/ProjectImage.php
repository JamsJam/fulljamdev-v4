<?php

namespace App\Entity\Project;

use App\Repository\Project\ProjectImageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectImageRepository::class)]
#[ORM\Table(name: 'project_image')]
class ProjectImage
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private string $path = '';

    #[ORM\Column(length: 255)]
    private string $originalName = '';

    /** @var Collection<int, Project> */
    #[ORM\ManyToMany(targetEntity: Project::class, mappedBy: 'images')]
    private Collection $projects;

    public function __construct()
    {
        $this->projects = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getPath(): string { return $this->path; }
    public function setPath(string $path): static { $this->path = $path; return $this; }
    public function getOriginalName(): string { return $this->originalName; }
    public function setOriginalName(string $originalName): static { $this->originalName = $originalName; return $this; }
    /** @return Collection<int, Project> */
    public function getProjects(): Collection { return $this->projects; }

    public function addProject(Project $project): static
    {
        if (!$this->projects->contains($project)) { $this->projects->add($project); }
        return $this;
    }

    public function removeProject(Project $project): static
    {
        $this->projects->removeElement($project);
        return $this;
    }
}
