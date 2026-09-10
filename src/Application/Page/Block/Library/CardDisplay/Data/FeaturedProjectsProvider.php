<?php

namespace App\Application\Page\Block\Library\CardDisplay\Data;

use App\Application\Page\Element\Cta\CtaTarget;
use App\Application\Page\Element\Image\ImageDTO;
use App\Application\Page\Element\Image\ImageSource;
use App\Application\Project\Provider\ProjectProvider;
use Symfony\Component\HttpFoundation\RequestStack;

final readonly class FeaturedProjectsProvider implements FeaturedProjectsProviderInterface
{
    public function __construct(private ProjectProvider $projects, private RequestStack $requests)
    {
    }

    public function provide(): array
    {
        $request = $this->requests->getCurrentRequest();
        if ($request?->attributes->has(self::class)) {
            return $request->attributes->get(self::class);
        }
        $cards = [];
        foreach ($this->projects->provideFeatured() as $project) {
            $card = new CardDisplayItemDTO();
            $card->title = $project->getTitle();
            $card->text = $project->getExcerpt() ?? '';
            $card->cta->label = 'Voir le projet';
            $card->cta->target = CtaTarget::ROUTE;
            $card->cta->routeName = 'app_front_project_show';
            $card->cta->routeParameters = ['slug' => $project->getSlug()];

            $mainImage = $project->getImages()->first();
            if (false !== $mainImage) {
                $card->image = new ImageDTO();
                $card->image->source = ImageSource::MEDIA;
                $card->image->mediaId = $mainImage->getPath();
                $card->image->alt = $project->getTitle();
            }

            $cards[] = $card;
        }

        $request?->attributes->set(self::class, $cards);

        return $cards;
    }
}
