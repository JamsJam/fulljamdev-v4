<?php

namespace App\Application\Page\Page\Routing;

use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RouterInterface;

final readonly class PagePathValidator
{
    public function __construct(private RouterInterface $router)
    {
    }

    public function isAvailable(string $path): bool
    {
        $context = clone $this->router->getContext();
        $context->setMethod('GET');
        $matcher = new UrlMatcher($this->router->getRouteCollection(), $context);

        return $this->isAvailableForMatcher($matcher, '/'.$path)
            && $this->isAvailableForMatcher($matcher, '/'.$path.'/');
    }

    private function isAvailableForMatcher(UrlMatcher $matcher, string $path): bool
    {
        try {
            $parameters = $matcher->match($path);
        } catch (ResourceNotFoundException) {
            return true;
        }

        return 'app_front_page' === ($parameters['_route'] ?? null);
    }
}
