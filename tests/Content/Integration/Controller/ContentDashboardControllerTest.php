<?php

namespace App\Tests\Content\Integration\Controller;

use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ContentDashboardControllerTest extends WebTestCase
{
    #[DataProvider('dashboardPages')]
    public function testDashboardPageIsRendered(string $path, string $heading): void
    {
        $client = self::createClient();
        $client->request('GET', $path);

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', $heading);
    }

    /** @return iterable<string, array{string, string}> */
    public static function dashboardPages(): iterable
    {
        yield 'CV' => ['/dashboard/cv', 'Expériences'];
        yield 'nouvelle expérience' => ['/dashboard/cv/new', 'Ajouter une expérience'];
        yield 'projets' => ['/dashboard/projet', 'Projets'];
        yield 'nouveau projet' => ['/dashboard/projet/new', 'Ajouter un projet'];
        yield 'blog' => ['/dashboard/blog', 'Blog'];
        yield 'nouvel article' => ['/dashboard/blog/article/new', 'Ajouter un article'];
        yield 'nouvelle catégorie' => ['/dashboard/blog/category/new', 'Ajouter une catégorie'];
    }
}
