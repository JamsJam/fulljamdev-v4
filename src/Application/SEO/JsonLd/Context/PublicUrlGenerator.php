<?php

namespace App\Application\SEO\JsonLd\Context;

use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\UrlHelper;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final readonly class PublicUrlGenerator
{
    public function __construct(
        private UrlGeneratorInterface $router,
        private Packages $assets,
        private UrlHelper $urls,
    ) {
    }

    /** @param array<string, mixed> $parameters */
    public function route(string $route, array $parameters = []): string
    {
        return $this->router->generate($route, $parameters, UrlGeneratorInterface::ABSOLUTE_URL);
    }

    public function canonical(?string $canonical, string $fallback): string
    {
        if (null === $canonical || '' === trim($canonical)) {
            return $fallback;
        }

        $url = $this->urls->getAbsoluteUrl(trim($canonical));
        $url = explode('#', $url, 2)[0];

        return $this->isHttpUrl($url) ? $url : $fallback;
    }

    public function optionalAbsolute(?string $url): ?string
    {
        if (null === $url || '' === trim($url)) {
            return null;
        }

        $absolute = $this->urls->getAbsoluteUrl(trim($url));

        return $this->isHttpUrl($absolute) ? $absolute : null;
    }

    /** @param list<string|null> $paths
     * @return list<string>
     */
    public function images(array $paths): array
    {
        $images = [];
        foreach ($paths as $path) {
            if (null === $path || '' === trim($path)) {
                continue;
            }
            $url = $this->urls->getAbsoluteUrl($this->assets->getUrl($path));
            if ($this->isHttpUrl($url)) {
                $images[] = $url;
            }
        }

        return array_values(array_unique($images));
    }

    private function isHttpUrl(string $url): bool
    {
        return false !== filter_var($url, FILTER_VALIDATE_URL) && in_array(parse_url($url, PHP_URL_SCHEME), ['http', 'https'], true);
    }
}
