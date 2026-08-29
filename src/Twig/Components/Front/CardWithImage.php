<?php

namespace App\Twig\Components\Front;

use App\Application\Page\Block\Library\CardDisplay\Data\CardDisplayItemDTO;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: 'components/front/card-display/CardWithImage.html.twig')]
final class CardWithImage
{
    public CardDisplayItemDTO $card;

    public function getImageUrl(): ?string
    {
        $image = $this->card->image;
        if (null !== $image?->mediaId && 1 === preg_match('/^[a-zA-Z0-9._-]+$/', $image->mediaId)) {
            return '/uploads/pages/'.$image->mediaId;
        }

        return null !== $image?->url && in_array(parse_url($image->url, PHP_URL_SCHEME), ['http', 'https'], true) ? $image->url : null;
    }
}
