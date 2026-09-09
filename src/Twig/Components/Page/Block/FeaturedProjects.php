<?php

namespace App\Twig\Components\Page\Block;

use App\Application\Page\Block\Library\CardDisplay\Data\CardDisplayItemDTO;
use App\Application\Page\Block\Library\CardDisplay\Data\FeaturedProjectsProviderInterface;
use App\Application\Page\Block\Library\Project\Featured\FeaturedProjectsDTO;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'Page:Block:Project:Featured',
    template: 'components/page/block/project/FeaturedProjects.html.twig',
)]
final class FeaturedProjects
{
    public FeaturedProjectsDTO $data;
    public ?int $blockId = null;

    public function __construct(private readonly FeaturedProjectsProviderInterface $projects)
    {
    }

    /** @return list<CardDisplayItemDTO> */
    public function getProjects(): array
    {
        return $this->projects->provide();
    }
}
