<?php

namespace App\Application\Page\Element\Cta;

use App\Application\Page\Element\Attribute\SafeHtmlAttributes;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class CtaDTO
{
    #[Assert\Length(max: 80)]
    public string $label = '';
    #[Assert\Length(max: 2048)]
    public string $href = '';
    public CtaTarget $target = CtaTarget::URL;
    #[Assert\Length(max: 255)]
    public ?string $routeName = null;
    /** @var array<string, scalar|null> */
    public array $routeParameters = [];
    /** @var array<string, scalar|null> */
    #[SafeHtmlAttributes]
    public array $attributes = [];

    #[Assert\Callback]
    public function validateHref(ExecutionContextInterface $context): void
    {
        if ('' === $this->label && '' === $this->href && (null === $this->routeName || '' === $this->routeName)) {
            return;
        }

        if ('' === $this->label) {
            $context->buildViolation('Saisissez le libellé du CTA.')->atPath('label')->addViolation();
        }

        if (CtaTarget::ROUTE === $this->target) {
            if (null === $this->routeName || '' === $this->routeName) {
                $context->buildViolation('Sélectionnez une route.')->atPath('routeName')->addViolation();
            }

            return;
        }

        if ('' === $this->href) {
            $context->buildViolation('Saisissez une URL.')->atPath('href')->addViolation();
        } elseif (!str_starts_with($this->href, '/') && false === filter_var($this->href, FILTER_VALIDATE_URL)) {
            $context->buildViolation('L’URL du CTA est invalide.')->atPath('href')->addViolation();
        }
    }
}
