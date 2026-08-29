<?php

namespace App\Tests\Experience\Application;

use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ExperienceDashboardTest extends WebTestCase
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
    }
}
