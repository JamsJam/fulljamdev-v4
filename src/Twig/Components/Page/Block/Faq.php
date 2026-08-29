<?php

namespace App\Twig\Components\Page\Block;

use App\Application\Page\Block\Library\Faq\Main\FaqDTO;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'Page:Block:Faq:Main',
    template: 'components/page/block/faq/Faq.html.twig',
)]
final class Faq
{
    public FaqDTO $data;
    public ?int $blockId = null;

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
