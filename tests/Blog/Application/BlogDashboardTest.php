<?php

namespace App\Tests\Blog\Application;

use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class BlogDashboardTest extends WebTestCase
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
        yield 'blog' => ['/dashboard/blog', 'Blog'];
        yield 'nouvel article' => ['/dashboard/blog/article/new', 'Ajouter un article'];
        yield 'nouvelle catégorie' => ['/dashboard/blog/category/new', 'Ajouter une catégorie'];
    }
}
