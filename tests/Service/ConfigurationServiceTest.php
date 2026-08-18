<?php

namespace App\Tests\Service;

use App\Service\ConfigurationService;
use PHPUnit\Framework\TestCase;

final class ConfigurationServiceTest extends TestCase
{
    private string $configurationFile;

    protected function setUp(): void
    {
        $this->configurationFile = tempnam(sys_get_temp_dir(), 'application-config-');
        file_put_contents($this->configurationFile, "parameters:\n    theme: light\n");
    }

    protected function tearDown(): void
    {
        if (is_file($this->configurationFile)) {
            unlink($this->configurationFile);
        }
    }

    public function testItReadsAndWritesNestedValuesWithoutLosingExistingConfiguration(): void
    {
        $configuration = new ConfigurationService($this->configurationFile);

        self::assertSame('light', $configuration->get('parameters.theme'));
        self::assertSame('fallback', $configuration->get('parameters.missing', 'fallback'));

        $configuration->set('parameters.timezone', 'America/Montreal');

        self::assertSame('America/Montreal', $configuration->get('parameters.timezone'));
        self::assertSame('light', $configuration->get('parameters.theme'));
    }
}
