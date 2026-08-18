<?php

namespace App\Twig\Components\Admin;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: 'components/admin/button/Button.html.twig')]
final class Button
{
    private const VARIANTS = [
        'primary',
        'secondary',
        'destructive',
        'success',
        'outline',
        'ghost',
        'icon',
        'withIcon',
        'loading',
    ];

    public string $variant = 'primary';
    public string $type = 'button';
    public ?string $href = null;
    public ?string $label = null;
    public ?string $icon = null;
    public string $iconPosition = 'start';
    public bool $disabled = false;
    public bool $onlyIcon = false;

    public function getVariantClass(): string
    {
        if (!in_array($this->variant, self::VARIANTS, true)) {
            throw new \InvalidArgumentException(sprintf('Unknown button variant "%s".', $this->variant));
        }

        return 'button--'.$this->variant;
    }

    public function isIconOnly(): bool
    {
        return $this->onlyIcon || 'icon' === $this->variant;
    }

    public function isLoading(): bool
    {
        return 'loading' === $this->variant;
    }
}
