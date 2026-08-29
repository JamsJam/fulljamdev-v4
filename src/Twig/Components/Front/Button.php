<?php

namespace App\Twig\Components\Front;

use App\Application\Page\Element\Cta\CtaDTO;
use App\Application\Page\Element\Cta\CtaTarget;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: 'components/front/button/Button.html.twig')]
final class Button
{
    private const VARIANTS = ['primary', 'secondary', 'cta'];

    public string $variant = 'primary';
    public string $type = 'button';
    public ?string $href = null;
    public ?string $label = null;
    public ?CtaDTO $cta = null;

    public function __construct(private readonly ?UrlGeneratorInterface $urlGenerator = null)
    {
    }

    public function getVariantClass(): string
    {
        if (!in_array($this->variant, self::VARIANTS, true)) {
            throw new \InvalidArgumentException(sprintf('Unknown front button variant "%s".', $this->variant));
        }

        return 'button--'.$this->variant;
    }

    public function getResolvedLabel(): string
    {
        if (null !== $this->cta) {
            return $this->cta->label;
        }

        return $this->label ?? 'Action';
    }

    public function getResolvedHref(): ?string
    {
        if (null === $this->cta) {
            return null === $this->href ? null : $this->safeHref($this->href);
        }

        if (CtaTarget::ROUTE === $this->cta->target && null !== $this->cta->routeName && null !== $this->urlGenerator) {
            try {
                return $this->urlGenerator->generate($this->cta->routeName, $this->cta->routeParameters);
            } catch (\InvalidArgumentException) {
                return '#';
            }
        }

        return $this->safeHref($this->cta->href);
    }

    public function getCtaAttributes(): string
    {
        if (null === $this->cta) {
            return '';
        }

        $html = '';
        foreach ($this->cta->attributes as $name => $value) {
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

    private function safeHref(string $href): string
    {
        if (str_starts_with($href, '/') && !str_starts_with($href, '//')) {
            return $href;
        }

        return in_array(parse_url($href, PHP_URL_SCHEME), ['http', 'https', 'mailto', 'tel'], true) ? $href : '#';
    }
}
