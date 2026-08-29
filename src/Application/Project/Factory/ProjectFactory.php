<?php

namespace App\Application\Project\Factory;

use App\Application\Project\Dto\ProjectDto;
use App\Entity\Content\Project;
use App\Service\HtmlSanitizerService;

final readonly class ProjectFactory
{
    public function __construct(private HtmlSanitizerService $sanitizer)
    {
    }

    public function fromEntity(Project $project): ProjectDto
    {
        $dto = new ProjectDto();
        $dto->title = $project->getTitle();
        $dto->slug = $project->getSlug();
        $dto->excerpt = $project->getExcerpt();
        $dto->content = $project->getContent();
        $dto->featuredImage = $project->getFeaturedImage();
        $dto->technologies = $project->getTechnologies();
        $dto->websiteUrl = $project->getWebsiteUrl();
        $dto->repositoryUrl = $project->getRepositoryUrl();
        $dto->isFeatured = $project->isFeatured();
        $dto->status = $project->getStatus();
        $dto->publishedAt = $project->getPublishedAt();

        return $dto;
    }

    public function create(ProjectDto $dto, ?Project $project = null): Project
    {
        return ($project ?? new Project())->setTitle($dto->title)->setSlug($dto->slug)->setExcerpt($dto->excerpt)->setContent($this->sanitizer->sanitize($dto->content))->setFeaturedImage($dto->featuredImage)->setTechnologies($dto->technologies)->setWebsiteUrl($dto->websiteUrl)->setRepositoryUrl($dto->repositoryUrl)->setIsFeatured($dto->isFeatured)->setStatus($dto->status)->setPublishedAt($dto->publishedAt);
    }
}
