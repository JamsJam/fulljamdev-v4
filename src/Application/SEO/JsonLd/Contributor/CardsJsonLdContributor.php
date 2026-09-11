<?php

namespace App\Application\SEO\JsonLd\Contributor;

use App\Application\Page\Block\Library\CardDisplay\Data\CardDisplayItemDTO;
use App\Application\Page\Block\Library\CardDisplay\Data\FeaturedProjectsProviderInterface;
use App\Application\Page\Block\Library\CardDisplay\Shared\CardDisplayDTO;
use App\Application\Page\Block\Library\Project\Featured\FeaturedProjectsDTO;
use App\Application\Page\Element\Cta\CtaTarget;
use App\Application\Page\Page\Dto\PageBlockDTO;
use App\Application\SEO\JsonLd\Builder\ItemListContributionBuilder;
use App\Application\SEO\JsonLd\Context\PublicUrlGenerator;
use App\Application\SEO\JsonLd\Dto\JsonLdContribution;
use App\Application\SEO\JsonLd\Interface\BlockJsonLdContributorInterface;

final readonly class CardsJsonLdContributor implements BlockJsonLdContributorInterface
{
    public function __construct(
        private FeaturedProjectsProviderInterface $projects,
        private PublicUrlGenerator $urls,
        private ItemListContributionBuilder $lists,
    ) {
    }

    public function supports(PageBlockDTO $block): bool
    {
        return ('project.featured' === $block->type && $block->data instanceof FeaturedProjectsDTO)
            || (in_array($block->type, ['card_display.with_image', 'services.main'], true) && $block->data instanceof CardDisplayDTO);
    }

    public function contribute(PageBlockDTO $block, string $blockId): JsonLdContribution
    {
        $data = $block->data;
        if (!$data instanceof CardDisplayDTO && !$data instanceof FeaturedProjectsDTO) {
            return new JsonLdContribution();
        }
        $cards = $data instanceof FeaturedProjectsDTO ? $this->projects->provide() : $data->cards;
        $service = 'services.main' === $block->type;
        $items = [];
        foreach ($cards as $index => $card) {
            if ('' === trim($card->title)) {
                continue;
            }
            $url = $this->cardUrl($card);
            $route = null !== $url && CtaTarget::ROUTE === $card->cta?->target ? $card->cta->routeName : null;
            $type = $service ? 'Service' : match ($route) {
                'app_front_article_show' => 'BlogPosting',
                'app_front_project_show' => 'CreativeWork',
                default => 'Thing',
            };
            // A service CTA can lead to a shared contact page: it is not its identity.
            $id = null !== $url && in_array($type, ['BlogPosting', 'CreativeWork'], true)
                ? $url.('BlogPosting' === $type ? '#article' : '#project')
                : $blockId.'-card-'.$index;
            $item = ['@type' => $type, '@id' => $id, 'name' => $card->title];
            if ('BlogPosting' === $type) {
                $item['headline'] = $card->title;
            }
            if ('' !== trim($card->text)) {
                $item['description'] = $card->text;
            }
            if (null !== $url && !$service) {
                $item['url'] = $url;
            }
            $items[] = $item;
        }

        return $this->lists->build($blockId.'-cards', $data->title->content, $items);
    }

    private function cardUrl(CardDisplayItemDTO $card): ?string
    {
        $cta = $card->cta;
        if (null === $cta || '' === trim($cta->label)) {
            return null;
        }
        if (CtaTarget::ROUTE === $cta->target) {
            if (null === $cta->routeName) {
                return null;
            }
            try {
                return $this->urls->route($cta->routeName, $cta->routeParameters);
            } catch (\InvalidArgumentException) {
                return null;
            }
        }
        if (!str_starts_with($cta->href, '/') && !in_array(parse_url($cta->href, PHP_URL_SCHEME), ['http', 'https'], true)) {
            return null;
        }
        if (str_starts_with($cta->href, '//')) {
            return null;
        }

        return $this->urls->optionalAbsolute($cta->href);
    }
}
