<?php

namespace App\Application\Project\Factory;

use App\Application\Project\Asset\ProjectImageUploader;
use App\Application\Project\Dto\ProjectDto;
use App\Entity\Project\Project;
use App\Entity\Project\ProjectImage;
use App\Service\HtmlSanitizerService;

final readonly class ProjectFactory
{
    public function __construct(private HtmlSanitizerService $sanitizer, private ProjectImageUploader $imageUploader)
    {
    }

    public function fromEntity(Project $project): ProjectDto
    {
        $dto = new ProjectDto();
        $dto->title = $project->getTitle();
        $dto->excerpt = $project->getExcerpt();
        $dto->content = $project->getContent();
        $dto->technologies = $project->getTechnologies()->toArray();
        $dto->websiteUrl = $project->getWebsiteUrl();
        $dto->repositoryUrl = $project->getRepositoryUrl();
        $dto->isFeatured = $project->isFeatured();
        $dto->status = $project->getStatus();
        $dto->publishedAt = $project->getPublishedAt();

        return $dto;
    }

    public function create(ProjectDto $dto, ?Project $project = null): Project
    {
        $project ??= new Project();
        $selected = [];
        foreach ($dto->technologies as $technology) {
            $selected[$technology->getId() ?? spl_object_id($technology)] = $technology;
            $project->addTechnology($technology);
        }
        foreach ($project->getTechnologies()->toArray() as $technology) {
            if (!isset($selected[$technology->getId() ?? spl_object_id($technology)])) {
                $project->removeTechnology($technology);
            }
        }

        foreach ($dto->imageFiles as $file) {
            $project->addImage((new ProjectImage())
                ->setPath($this->imageUploader->upload($file))
                ->setOriginalName($file->getClientOriginalName()));
        }

        return $project->setTitle($dto->title)->setExcerpt($dto->excerpt)->setContent($this->sanitizer->sanitize($dto->content))->setWebsiteUrl($dto->websiteUrl)->setRepositoryUrl($dto->repositoryUrl)->setIsFeatured($dto->isFeatured);
    }
}
