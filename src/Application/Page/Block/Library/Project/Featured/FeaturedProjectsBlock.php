<?php

namespace App\Application\Page\Block\Library\Project\Featured;

use App\Application\Page\Block\Interface\BlockDefinitionInterface;

final class FeaturedProjectsBlock implements BlockDefinitionInterface
{
    public function type(): string
    {
        return 'project.featured';
    }

    public function label(): string
    {
        return 'Projets mis en avant';
    }

    public function category(): string
    {
        return 'Projet';
    }

    public function dtoClass(): string
    {
        return FeaturedProjectsDTO::class;
    }

    public function formType(): string
    {
        return FeaturedProjectsType::class;
    }

    public function component(): string
    {
        return 'Page:Block:Project:Featured';
    }

    public function formTemplate(): string
    {
        return 'dashboard/page/block/_featured_projects_form.html.twig';
    }

    public function createDefaultData(): object
    {
        return new FeaturedProjectsDTO();
    }
}
