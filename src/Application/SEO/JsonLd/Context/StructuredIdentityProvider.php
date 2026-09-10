<?php

namespace App\Application\SEO\JsonLd\Context;

use App\Application\SEO\JsonLd\Dto\IdentityData;
use App\Application\Settings\Service\GetGeneralSettingsService;

final readonly class StructuredIdentityProvider
{
    public function __construct(private GetGeneralSettingsService $settings, private PublicUrlGenerator $urls)
    {
    }

    public function provide(): ?IdentityData
    {
        $settings = $this->settings->get();
        if (!in_array($settings->structuredIdentityType, ['Person', 'Organization'], true)
            || null === $settings->structuredIdentityName || '' === trim($settings->structuredIdentityName)) {
            return null;
        }

        $homeUrl = $this->urls->route('app_home');
        $configuredUrl = $this->urls->optionalAbsolute($settings->structuredIdentityUrl);
        $sameAs = array_column($settings->structuredIdentitySameAs, 'value');
        if (null !== $configuredUrl && parse_url($configuredUrl, PHP_URL_HOST) !== parse_url($homeUrl, PHP_URL_HOST)) {
            // Backward compatibility: the previous field commonly contained a LinkedIn URL.
            $sameAs[] = $configuredUrl;
            $configuredUrl = null;
        }

        return new IdentityData(
            type: $settings->structuredIdentityType,
            name: trim($settings->structuredIdentityName),
            id: rtrim($homeUrl, '/').'/#'.strtolower($settings->structuredIdentityType),
            url: $configuredUrl ?? $homeUrl,
            logo: 'Organization' === $settings->structuredIdentityType
                ? ($this->urls->images([$settings->logoPath])[0] ?? null)
                : null,
            sameAs: array_values(array_unique($sameAs)),
        );
    }

    public function isArticleAuthor(): bool
    {
        return $this->settings->get()->structuredIdentityIsArticleAuthor;
    }
}
