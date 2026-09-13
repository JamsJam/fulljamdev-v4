<?php

namespace App\Application\SEO\JsonLd\Contributor;

use App\Application\Page\Block\Library\Pricing\Main\PricingDTO;
use App\Application\Page\Block\Library\Pricing\Main\PricingPeriod;
use App\Application\Page\Page\Dto\PageBlockDTO;
use App\Application\SEO\JsonLd\Dto\JsonLdContribution;
use App\Application\SEO\JsonLd\Interface\BlockJsonLdContributorInterface;

final readonly class PricingJsonLdContributor implements BlockJsonLdContributorInterface
{
    public function supports(PageBlockDTO $block): bool
    {
        return 'pricing.main' === $block->type && $block->data instanceof PricingDTO;
    }

    public function contribute(PageBlockDTO $block, string $blockId): JsonLdContribution
    {
        if (!$block->data instanceof PricingDTO) {
            return new JsonLdContribution();
        }

        $offers = [];
        foreach ($block->data->cards as $index => $card) {
            if ('' === trim($card->title)) {
                continue;
            }

            $offer = [
                '@type' => 'Offer',
                '@id' => $blockId.'-offer-'.$index,
                'name' => $card->title,
                'price' => number_format($card->price / 100, 2, '.', ''),
                'priceCurrency' => 'EUR',
            ];
            if ('' !== trim($card->description)) {
                $offer['description'] = $card->description;
            }
            if (PricingPeriod::FIXED !== $card->period) {
                $offer['priceSpecification'] = [
                    '@type' => 'UnitPriceSpecification',
                    'price' => $offer['price'],
                    'priceCurrency' => 'EUR',
                    'billingDuration' => [
                        '@type' => 'QuantitativeValue',
                        'value' => 1,
                        'unitCode' => match ($card->period) {
                            PricingPeriod::DAILY => 'DAY',
                            PricingPeriod::MONTHLY => 'MON',
                            PricingPeriod::YEARLY => 'ANN',
                            PricingPeriod::FIXED => throw new \LogicException('A fixed price has no billing duration.'),
                        },
                    ],
                ];
            }
            $offers[] = $offer;
        }

        return new JsonLdContribution(nodes: $offers, mentions: array_column($offers, '@id'));
    }
}
