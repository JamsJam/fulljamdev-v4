<?php

namespace App\Tests\Page\Application;

use App\Application\Settings\General\Cache\GeneralSettingsCache;
use App\Application\Settings\General\Dto\GeneralSettingsDto;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

final class MaintenanceTest extends WebTestCase
{
    #[DataProvider('publicRequests')]
    public function testVisitorsSeeMaintenance(string $method, string $path): void
    {
        $client = $this->clientWithMaintenance();
        $client->request($method, $path);

        self::assertResponseStatusCodeSame(503);
        self::assertSelectorTextSame('h1', 'De retour très bientôt.');
        self::assertSelectorTextContains('.message', 'Message de test');
        self::assertSelectorExists('a[href="https://example.com/profil"]');
        self::assertResponseHeaderSame('cache-control', 'no-store, private');
        self::assertResponseNotHasHeader('Location');
    }

    public static function publicRequests(): iterable
    {
        yield ['GET', '/'];
        yield ['GET', '/contact'];
        yield ['GET', '/blog'];
        yield ['GET', '/book-meeting/test'];
        yield ['POST', '/book-meeting/test'];
        yield ['GET', '/inconnu.html'];
    }

    public function testLoginRemainsAccessible(): void
    {
        $client = $this->clientWithMaintenance();
        $client->request('GET', '/login');
        self::assertResponseIsSuccessful();
        self::assertSelectorNotExists('#maintenance-title');
    }

    public function testAuthenticatedVisitorsBypassMaintenance(): void
    {
        $client = $this->clientWithMaintenance();
        $provider = self::getContainer()->get('security.user.provider.concrete.test_user_provider');
        $client->loginUser($provider->loadUserByIdentifier('user@example.test'));
        $client->request('GET', '/');
        // No homepage is configured: reaching its normal 404 proves the bypass.
        self::assertResponseStatusCodeSame(404);
        self::assertSelectorTextSame('h1', 'Cette page est introuvable.');
    }

    public function testDisabledMaintenanceLeavesNormalResponse(): void
    {
        $client = $this->clientWithMaintenance(false);
        $client->request('GET', '/');
        self::assertResponseStatusCodeSame(404);
        self::assertSelectorNotExists('#maintenance-title');
    }

    public function testDashboardKeepsItsAuthenticationProtection(): void
    {
        $client = $this->clientWithMaintenance();
        $client->request('GET', '/dashboard/settings');
        self::assertResponseRedirects('/login');
    }

    private function clientWithMaintenance(bool $enabled = true): KernelBrowser
    {
        $client = self::createClient(['debug' => false]);
        $settings = new GeneralSettingsDto();
        $settings->maintenanceEnabled = $enabled;
        $settings->maintenanceMessage = 'Message de test';
        $settings->maintenanceLinks = [['name' => 'Mon profil', 'value' => 'https://example.com/profil']];
        $cache = new ArrayAdapter();
        $cache->get(GeneralSettingsCache::KEY, static fn () => $settings);
        self::getContainer()->set(GeneralSettingsCache::class, new GeneralSettingsCache($cache));

        return $client;
    }
}
