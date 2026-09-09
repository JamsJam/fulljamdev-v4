<?php

namespace App\Twig\Components\Front;

use App\Application\Page\Block\Library\CardDisplay\Data\CardDisplayItemDTO;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: 'components/front/card-display/CardWithLogo.html.twig')]
final class CardWithLogo
{
    public CardDisplayItemDTO $card;

    public function getLogoUrl(): ?string
    {
        return $this->imageUrl($this->card->logo?->mediaId, $this->card->logo?->url);
    }

    private function imageUrl(?string $mediaId, ?string $url): ?string
    {
        if (null !== $mediaId && 1 === preg_match('/^[a-zA-Z0-9._-]+$/', $mediaId)) {
            return '/uploads/pages/'.$mediaId;
        }

        return null !== $url && in_array(parse_url($url, PHP_URL_SCHEME), ['http', 'https'], true) ? $url : null;
    }
}
