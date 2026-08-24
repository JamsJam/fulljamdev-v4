<?php

namespace App\Tests\Settings\Application\Account;

use App\Application\Settings\Account\Cache\AccountSettingsCache;
use App\Application\Settings\Account\Provider\AccountSettingsProvider;
use App\Application\Settings\Account\Proxy\AccountSettingsProxy;
use App\Application\Settings\Account\Writer\AccountSettingsWriter;
use App\Application\Settings\Service\GetAccountSettingsService;
use App\Application\Settings\Service\UpdateAccountSettingsService;
use App\Application\Settings\Storage\YamlSettingsStorage;
use App\Service\Yaml\YamlParserService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

final class AccountSettingsServiceTest extends TestCase
{
    private string $yamlFile;

    protected function setUp(): void
    {
        $this->yamlFile = (string) tempnam(sys_get_temp_dir(), 'account-settings-');
        file_put_contents($this->yamlFile, "account:\n    first_name: Ada\n    last_name: Lovelace\n    email: ada@example.test\n");
    }

    protected function tearDown(): void
    {
        if (is_file($this->yamlFile)) {
            unlink($this->yamlFile);
        }
    }

    public function testUpdateInvalidatesTheDtoCache(): void
    {
        $storage = new YamlSettingsStorage(new YamlParserService(), $this->yamlFile);
        $cache = new AccountSettingsCache(new ArrayAdapter());
        $reader = new GetAccountSettingsService(new AccountSettingsProxy($cache, new AccountSettingsProvider($storage)));
        $writer = new UpdateAccountSettingsService(new AccountSettingsWriter($storage), $cache);
        $settings = $reader->get();

        self::assertSame('Ada', $settings->firstName);
        $settings->firstName = 'Grace';
        $writer->update($settings);

        self::assertSame('Grace', $reader->get()->firstName);
    }

    public function testUpdatePreservesOtherConfigurationDomains(): void
    {
        file_put_contents($this->yamlFile, "parameters:\n    timezone: Europe/Paris\naccount:\n    first_name: Ada\n");
        $storage = new YamlSettingsStorage(new YamlParserService(), $this->yamlFile);
        $cache = new AccountSettingsCache(new ArrayAdapter());
        $reader = new GetAccountSettingsService(new AccountSettingsProxy($cache, new AccountSettingsProvider($storage)));
        $settings = $reader->get();
        $settings->firstName = 'Grace';

        (new UpdateAccountSettingsService(new AccountSettingsWriter($storage), $cache))->update($settings);

        self::assertSame('Europe/Paris', $storage->read()['parameters']['timezone']);
    }

    public function testUpdatePropagatesAStorageFailure(): void
    {
        $missingFile = sys_get_temp_dir().'/missing-account-settings-directory/config.yaml';
        $storage = new YamlSettingsStorage(new YamlParserService(), $missingFile);
        $cache = new AccountSettingsCache(new ArrayAdapter());

        $this->expectException(\RuntimeException::class);
        (new UpdateAccountSettingsService(new AccountSettingsWriter($storage), $cache))->update(
            (new AccountSettingsProvider(new YamlSettingsStorage(new YamlParserService(), $this->yamlFile)))->provide(),
        );
    }
}
