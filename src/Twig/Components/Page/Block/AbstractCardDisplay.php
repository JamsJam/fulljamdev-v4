<?php

namespace App\Twig\Components\Page\Block;

use App\Application\Page\Block\Library\CardDisplay\Data\CardDisplayItemDTO;
use App\Application\Page\Block\Library\CardDisplay\Shared\CardDisplayDTO;

abstract class AbstractCardDisplay
{
    public CardDisplayDTO $data;
    public ?int $blockId = null;

    /** @return list<CardDisplayItemDTO> */
    public function getCards(): array
    {
        return $this->data->cards;
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
