<?php

namespace App\Service;

use Symfony\Component\Yaml\Yaml;

final readonly class ConfigurationService
{
    public function __construct(private string $configurationFile)
    {
    }

    /** @return array<string, mixed> */
    public function all(): array
    {
        if (!is_file($this->configurationFile)) {
            return [];
        }

        $configuration = Yaml::parseFile($this->configurationFile);

        return is_array($configuration) ? $configuration : [];
    }

    public function get(string $path, mixed $default = null): mixed
    {
        $value = $this->all();

        foreach (explode('.', $path) as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }

            $value = $value[$segment];
        }

        return $value;
    }

    public function set(string $path, mixed $value): void
    {
        $configuration = $this->all();
        $cursor = &$configuration;

        foreach (explode('.', $path) as $segment) {
            if (!isset($cursor[$segment]) || !is_array($cursor[$segment])) {
                $cursor[$segment] = [];
            }

            $cursor = &$cursor[$segment];
        }

        $cursor = $value;
        $this->write($configuration);
    }

    /** @param array<string, mixed> $configuration */
    public function write(array $configuration): void
    {
        $directory = dirname($this->configurationFile);
        if (!is_dir($directory) || !is_writable($directory)) {
            throw new \RuntimeException(sprintf('Le dossier de configuration « %s » n’est pas accessible en écriture.', $directory));
        }

        $temporaryFile = tempnam($directory, '.config-');
        if (false === $temporaryFile) {
            throw new \RuntimeException('Impossible de créer le fichier de configuration temporaire.');
        }

        try {
            $yaml = Yaml::dump($configuration, 6, 4);
            if (false === file_put_contents($temporaryFile, $yaml, LOCK_EX) || !rename($temporaryFile, $this->configurationFile)) {
                throw new \RuntimeException('Impossible d’enregistrer la configuration.');
            }
        } finally {
            if (is_file($temporaryFile)) {
                unlink($temporaryFile);
            }
        }
    }
}
