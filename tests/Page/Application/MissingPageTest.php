<?php

namespace App\Tests\Page\Application;

use App\Application\Settings\General\Cache\GeneralSettingsCache;
use App\Application\Settings\General\Dto\GeneralSettingsDto;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

final class MissingPageTest extends WebTestCase
{
    public function testMissingPublicPageReturnsTheCustom404(): void
    {
        $client = self::createClient(['debug' => false]);
        $container = self::getContainer();
        // Use an empty, isolated database rather than the application's pages.
        $container->set('doctrine.dbal.default_connection', DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'memory' => true,
        ]));
        $entityManager = $container->get(EntityManagerInterface::class);
        (new SchemaTool($entityManager))->createSchema($entityManager->getMetadataFactory()->getAllMetadata());

        $client->request('GET', '/page-inexistante');

        $this->assertMissingPageResponse();
    }

    public function testHomepageWithoutConfiguredPageReturnsTheCustom404(): void
    {
        $client = self::createClient(['debug' => false]);
        // Isolate the settings in memory without modifying the site's YAML or cache.
        $cache = new ArrayAdapter();
        $cache->get(GeneralSettingsCache::KEY, static fn (): GeneralSettingsDto => new GeneralSettingsDto());
        self::getContainer()->set(GeneralSettingsCache::class, new GeneralSettingsCache($cache));

        $client->request('GET', '/');

        $this->assertMissingPageResponse();
    }

    private function assertMissingPageResponse(): void
    {
        self::assertResponseStatusCodeSame(404);
        self::assertResponseHeaderSame('content-type', 'text/html; charset=UTF-8');
        self::assertResponseNotHasHeader('Location');
        self::assertSelectorTextSame('h1', 'Cette page est introuvable.');
        self::assertSelectorExists('meta[name="robots"][content="noindex, nofollow"]');
        self::assertSelectorExists('a[href="/"]');
    }
}
