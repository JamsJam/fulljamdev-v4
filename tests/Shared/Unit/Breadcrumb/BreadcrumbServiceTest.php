<?php

namespace App\Tests\Shared\Unit\Breadcrumb;

use App\Service\Breadcrumb\BreadcrumbBuilder;
use App\Service\Breadcrumb\BreadcrumbMapper;
use App\Service\Breadcrumb\BreadcrumbService;
use PHPUnit\Framework\TestCase;

final class BreadcrumbServiceTest extends TestCase
{
    /**
     * @param list<string> $expectedLabels
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('childRouteProvider')]
    public function testItBuildsBreadcrumbsForDashboardChildRoutes(string $route, array $expectedLabels): void
    {
        $breadcrumb = $this->createService()->getBreadcrumb($route);

        self::assertSame($expectedLabels, array_column($breadcrumb, 'label'));
    }

    /**
     * @return iterable<string, array{string, list<string>}>
     */
    public static function childRouteProvider(): iterable
    {
        yield 'blog' => ['app_dashboard_blog_article_new', ['Fulljamdev', 'Dashboard', 'Blog', 'Articles', 'Ajouter']];
        yield 'project' => ['app_dashboard_project_new', ['Fulljamdev', 'Dashboard', 'Projets', 'Ajouter']];
        yield 'cv' => ['app_dashboard_cv_edit', ['Fulljamdev', 'Dashboard', 'CV', 'Modifier']];
    }

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
