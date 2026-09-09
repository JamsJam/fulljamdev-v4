<?php

namespace App\Twig\Components\Page\Block;

use App\Application\Page\Block\Library\CardDisplay\Data\CardDisplayItemDTO;
use App\Application\Page\Block\Library\CardDisplay\Data\FeaturedProjectsProviderInterface;
use App\Application\Page\Block\Library\CardDisplay\Shared\CardDisplayDTO;
use App\Application\Page\Data\Enum\ValueSource;

abstract class AbstractCardDisplay
{
    public CardDisplayDTO $data;
    public ?int $blockId = null;

    public function __construct(private readonly FeaturedProjectsProviderInterface $featuredProjects)
    {
    }

    /** @return list<CardDisplayItemDTO> */
    public function getCards(): array
    {
        if (ValueSource::STATIC === $this->data->source) {
            return $this->data->cards;
        }

        return 'featured_projects' === $this->data->sourceKey ? $this->featuredProjects->provide() : [];
    }

    /** @param array<string, mixed> $attributes */
    public function safeAttributes(array $attributes): string
    {
        $html = '';
        foreach ($attributes as $name => $value) {
            if (!is_scalar($value) && null !== $value) {
                continue;
            }
            $normalizedName = strtolower($name);
            if (!in_array($normalizedName, ['id', 'target', 'rel', 'title'], true)
                && !str_starts_with($normalizedName, 'aria-')
                && !str_starts_with($normalizedName, 'data-')) {
                continue;
            }
            $html .= sprintf(' %s="%s"', htmlspecialchars($name, ENT_QUOTES), htmlspecialchars((string) $value, ENT_QUOTES));
        }

        return $html;
    }
}
