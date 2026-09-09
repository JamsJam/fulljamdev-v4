<?php

namespace App\Twig\Components\Page\Block;

abstract class AbstractHero
{
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

    public function safeImageUrl(?string $url): ?string
    {
        if (null === $url) {
            return null;
        }

        return in_array(parse_url($url, PHP_URL_SCHEME), ['http', 'https'], true) ? $url : null;
    }

    public function safeMediaUrl(?string $mediaId): ?string
    {
        if (null === $mediaId || 1 !== preg_match('/^[a-zA-Z0-9._-]+$/', $mediaId)) {
            return null;
        }

        return '/uploads/pages/'.$mediaId;
    }
}
