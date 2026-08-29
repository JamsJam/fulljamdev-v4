<?php

namespace App\Application\Page\Block\Library\CardDisplay\Data;

use App\Application\Page\Element\Cta\CtaTarget;
use App\Application\Page\Element\Image\ImageDTO;
use App\Application\Page\Element\Image\ImageSource;
use App\Application\Project\Provider\ProjectProvider;

final readonly class FeaturedProjectsProvider implements FeaturedProjectsProviderInterface
{
    public function __construct(private ProjectProvider $projects)
    {
    }

    public function provide(): array
    {
        $cards = [];
        foreach ($this->projects->provideFeatured() as $project) {
            $card = new CardDisplayItemDTO();
            $card->title = $project->getTitle();
            $card->text = $project->getExcerpt() ?? '';
            $card->cta->label = 'Voir le projet';
            $card->cta->target = CtaTarget::URL;
            $card->cta->href = '/projet/'.$project->getSlug();

            if (null !== $project->getFeaturedImage()) {
                $card->image = new ImageDTO();
                $card->image->source = ImageSource::MEDIA;
                $card->image->mediaId = $project->getFeaturedImage();
                $card->image->alt = $project->getTitle();
            }

            $cards[] = $card;
        }

        return $cards;
    }
}
