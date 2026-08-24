<?php

namespace App\Tests\Shared\Unit\Breadcrumb;

use App\Service\Breadcrumb\BreadcrumbBuilder;
use App\Service\Breadcrumb\BreadcrumbMapper;
use App\Service\Breadcrumb\BreadcrumbService;
use PHPUnit\Framework\TestCase;

final class BreadcrumbServiceTest extends TestCase
{
    public function testItBuildsTheBreadcrumbFromTheRouteElements(): void
    {
        $service = $this->createService();

        self::assertSame(
            [
                [
                    'label' => 'Fulljamdev',
                    'route' => 'app_home',
                ],
                [
                    'label' => 'Dashboard',
                    'route' => 'app_dashboard',
                ],
            ],
            $service->getBreadcrumb('app_dashboard'),
        );
    }

    public function testItRejectsAnEmptyRoute(): void
    {
        $service = $this->createService();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The route cannot be empty.');

        $service->getBreadcrumb('');
    }

    public function testItRejectsAnUnknownElement(): void
    {
        $service = $this->createService();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The breadcrumb element "unknown" is not configured.');

        $service->getBreadcrumb('app_unknown');
    }

    public function testItRejectsARouteContainingAnEmptyElement(): void
    {
        $service = $this->createService();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('A breadcrumb element cannot be empty.');

        $service->getBreadcrumb('app__dashboard');
    }

    private function createService(): BreadcrumbService
    {
        return new BreadcrumbService(new BreadcrumbBuilder(new BreadcrumbMapper()));
    }
}
