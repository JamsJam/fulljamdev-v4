<?php

namespace App\Twig\Components\Page\Block;

use App\Application\Page\Block\Hero\Main\HeroDTO;
use App\Application\Page\Element\Cta\CtaDTO;
use App\Application\Page\Element\Cta\CtaTarget;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Page:Block:Hero', template: 'components/page/block/Hero.html.twig')]
final class Hero
{
    public HeroDTO $data;

    public function __construct(private readonly ?UrlGeneratorInterface $urlGenerator = null)
    {
    }

    public function ctaHref(CtaDTO $cta): string
    {
        if (CtaTarget::ROUTE === $cta->target && null !== $cta->routeName && null !== $this->urlGenerator) {
            try {
                return $this->urlGenerator->generate($cta->routeName, $cta->routeParameters);
            } catch (\InvalidArgumentException) {
                return '#';
            }
        }

        return $this->safeHref($cta->href);
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

    public function safeHref(string $href): string
    {
        if (str_starts_with($href, '/') && !str_starts_with($href, '//')) {
            return $href;
        }

        $scheme = parse_url($href, PHP_URL_SCHEME);

        return in_array($scheme, ['http', 'https', 'mailto', 'tel'], true) ? $href : '#';
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
