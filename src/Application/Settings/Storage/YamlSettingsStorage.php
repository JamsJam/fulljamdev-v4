<?php

namespace App\Application\Settings\Storage;

use App\Service\Yaml\YamlParserService;

final readonly class YamlSettingsStorage
{
    public function __construct(
        private YamlParserService $yamlParser,
        private string $settingsConfigurationFile,
    ) {
    }

    /** @return array<string, mixed> */
    public function read(): array
    {
        return $this->yamlParser->parseFile($this->settingsConfigurationFile);
    }

    /** @param array<string, mixed> $settings */
    public function write(array $settings): void
    {
        $this->yamlParser->dumpFile($this->settingsConfigurationFile, $settings);
    }
}
