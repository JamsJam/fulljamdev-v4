<?php

namespace App\Tests\Page\Integration\Routing;

use App\Application\Page\Page\Routing\PagePathValidator;
use App\Application\Page\Page\Service\CheckPagePathAvailabilityService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RouterInterface;

final class PagePathValidatorTest extends KernelTestCase
{
    private PagePathValidator $validator;

    public function testGooglePathsAreReservedAndOAuthRoutesStillMatch(): void
    {
        $router = self::getContainer()->get(RouterInterface::class);
        $context = clone $router->getContext();
        $context->setMethod('GET');
        $matcher = new UrlMatcher($router->getRouteCollection(), $context);

        self::assertSame('app_google_connect', $matcher->match('/google/connect')['_route']);
        self::assertSame('app_google_callback', $matcher->match('/google/callback')['_route']);
        foreach (['google', 'google/', 'google/connect', 'google/callback', 'google/unknown'] as $path) {
            self::assertFalse($this->validator->isAvailable($path));
        }
        self::assertTrue($this->validator->isAvailable('google-services'));

        foreach (['/google', '/google/unknown'] as $path) {
            try {
                $matcher->match($path);
                self::fail('Google paths must not match a page fallback.');
            } catch (\Symfony\Component\Routing\Exception\ResourceNotFoundException) {
                self::assertTrue(true);
            }
        }
    }

    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = self::getContainer()->get(PagePathValidator::class);
    }

    public function testItRejectsPathsAlreadyHandledByPublicApplicationRoutes(): void
    {
        self::assertFalse($this->validator->isAvailable('book-meeting/consultation'));
    }

    public function testItRejectsPathsAlreadyHandledByDashboardRoutes(): void
    {
        self::assertFalse($this->validator->isAvailable('dashboard/settings/general'));
    }

    public function testItAcceptsRootAndNestedPathsHandledByThePageCatchAllRoute(): void
    {
        self::assertTrue($this->validator->isAvailable('services'));
        self::assertTrue($this->validator->isAvailable('services/developpement'));
    }

    public function testAvailabilityFacadeUsesTheSameRealRouterRules(): void
    {
        $service = self::getContainer()->get(CheckPagePathAvailabilityService::class);

        self::assertTrue($service->conflictsWithApplicationRoute('dashboard/reservations'));
        self::assertFalse($service->conflictsWithApplicationRoute('services/formation'));
    }

    public function testMissingPageFallbackDoesNotReserveTrailingSlashPaths(): void
    {
        $router = self::getContainer()->get(RouterInterface::class);
        $context = clone $router->getContext();
        $context->setMethod('GET');
        $matcher = new UrlMatcher($router->getRouteCollection(), $context);

        self::assertSame('app_front_missing_page', $matcher->match('/services/formation/')['_route']);
        self::assertTrue($this->validator->isAvailable('services/formation'));
        self::assertFalse($this->validator->isAvailable('login'));
    }
}
