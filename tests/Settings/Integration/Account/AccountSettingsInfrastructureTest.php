<?php

namespace App\Tests\Settings\Integration\Account;

use App\Application\Settings\Account\Cache\AccountSettingsCache;
use App\Application\Settings\Account\Writer\AccountSettingsWriter;
use App\Application\Settings\Service\GetAccountSettingsService;
use App\Application\Settings\Storage\YamlSettingsStorage;
use App\Service\Yaml\YamlParserService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class AccountSettingsInfrastructureTest extends KernelTestCase
{
    private string $yamlFile;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->yamlFile = (string) tempnam(sys_get_temp_dir(), 'account-infrastructure-');
        file_put_contents($this->yamlFile, "account:\n    first_name: Ada\n    email: ada@example.test\n");

        self::getContainer()->set(YamlSettingsStorage::class, new YamlSettingsStorage(
            self::getContainer()->get(YamlParserService::class),
            $this->yamlFile,
        ));
        self::getContainer()->get(AccountSettingsCache::class)->invalidate();
    }

    protected function tearDown(): void
    {
        if (is_file($this->yamlFile)) {
            unlink($this->yamlFile);
        }
    }

    public function testProviderMapsMissingOptionalValuesToEmptyStrings(): void
    {
        $settings = self::getContainer()->get(GetAccountSettingsService::class)->get();

        self::assertSame('Ada', $settings->firstName);
        self::assertSame('', $settings->lastName);
        self::assertSame('', $settings->phoneNumber);
    }

    public function testProxyReadsTheProviderOnlyAfterCacheInvalidation(): void
    {
        $parser = self::getContainer()->get(YamlParserService::class);
        $cache = self::getContainer()->get(AccountSettingsCache::class);
        $service = self::getContainer()->get(GetAccountSettingsService::class);

        self::assertSame('Ada', $service->get()->firstName);
        $parser->dumpFile($this->yamlFile, ['account' => ['first_name' => 'Grace']]);
        self::assertSame('Ada', $service->get()->firstName);

        $cache->invalidate();
        self::assertSame('Grace', $service->get()->firstName);
    }

    public function testCacheReturnsIndependentDtoInstances(): void
    {
        $service = self::getContainer()->get(GetAccountSettingsService::class);
        $first = $service->get();
        $first->firstName = 'Modified without writing';

        self::assertSame('Ada', $service->get()->firstName);
    }

    public function testWriterPreservesConfigurationOutsideTheAccountDomain(): void
    {
        $parser = self::getContainer()->get(YamlParserService::class);
        $parser->dumpFile($this->yamlFile, [
            'parameters' => ['timezone' => 'Europe/Paris'],
            'account' => ['first_name' => 'Ada'],
        ]);
        $dto = self::getContainer()->get(GetAccountSettingsService::class)->get();
        $dto->firstName = 'Grace';

        self::getContainer()->get(AccountSettingsWriter::class)->write($dto);

        $configuration = $parser->parseFile($this->yamlFile);
        self::assertSame('Grace', $configuration['account']['first_name']);
        self::assertSame('Europe/Paris', $configuration['parameters']['timezone']);
    }
}
