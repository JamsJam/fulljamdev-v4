<?php

namespace App\Tests\Page\Integration\Routing;

use App\Application\Page\Page\Routing\PagePathValidator;
use App\Application\Page\Page\Service\CheckPagePathAvailabilityService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class PagePathValidatorTest extends KernelTestCase
{
    private PagePathValidator $validator;

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
}
