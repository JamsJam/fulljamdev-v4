<?php

namespace App\Tests\Settings\Integration\General;

use App\Application\Settings\General\Cache\GeneralSettingsCache;
use App\Application\Settings\General\Writer\GeneralSettingsWriter;
use App\Application\Settings\Service\GetGeneralSettingsService;
use App\Application\Settings\Storage\YamlSettingsStorage;
use App\Service\Yaml\YamlParserService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class GeneralSettingsInfrastructureTest extends KernelTestCase
{
    private string $yamlFile;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->yamlFile = (string) tempnam(sys_get_temp_dir(), 'general-settings-infrastructure-');
        file_put_contents($this->yamlFile, "parameters:\n    timezone: America/Montreal\npages:\n    homepage_id: 42\n");

        self::getContainer()->set(YamlSettingsStorage::class, new YamlSettingsStorage(
            self::getContainer()->get(YamlParserService::class),
            $this->yamlFile,
        ));
        self::getContainer()->get(GeneralSettingsCache::class)->invalidate();
    }

    protected function tearDown(): void
    {
        if (is_file($this->yamlFile)) {
            unlink($this->yamlFile);
        }
    }

    public function testServiceReadsTypedGeneralSettingsThroughProviderProxyAndCache(): void
    {
        $settings = self::getContainer()->get(GetGeneralSettingsService::class)->get();

        self::assertSame('America/Montreal', $settings->timezone);
        self::assertSame(42, $settings->homepagePageId);
    }

    public function testProxyKeepsCachedValueUntilCacheInvalidation(): void
    {
        $service = self::getContainer()->get(GetGeneralSettingsService::class);
        $parser = self::getContainer()->get(YamlParserService::class);

        self::assertSame('America/Montreal', $service->get()->timezone);
        $parser->dumpFile($this->yamlFile, ['parameters' => ['timezone' => 'Asia/Tokyo']]);
        self::assertSame('America/Montreal', $service->get()->timezone);

        self::getContainer()->get(GeneralSettingsCache::class)->invalidate();
        self::assertSame('Asia/Tokyo', $service->get()->timezone);
    }

    public function testCacheReturnsIndependentDtoInstances(): void
    {
        $service = self::getContainer()->get(GetGeneralSettingsService::class);
        $first = $service->get();
        $first->timezone = 'UTC';

        self::assertSame('America/Montreal', $service->get()->timezone);
    }

    public function testWriterPreservesConfigurationOutsideTheGeneralDomain(): void
    {
        $service = self::getContainer()->get(GetGeneralSettingsService::class);
        $settings = $service->get();
        $settings->timezone = 'Europe/Paris';
        $settings->homepagePageId = 84;

        self::getContainer()->get(GeneralSettingsWriter::class)->write($settings);

        $configuration = self::getContainer()->get(YamlParserService::class)->parseFile($this->yamlFile);
        self::assertSame('Europe/Paris', $configuration['parameters']['timezone']);
        self::assertSame(84, $configuration['pages']['homepage_id']);
    }
}
