<?php

namespace App\Service\Yaml;

use Symfony\Component\Yaml\Yaml;

final class YamlParserService
{
    /** @return array<string, mixed> */
    public function parseFile(string $filename): array
    {
        if (!is_file($filename)) {
            return [];
        }

        $data = Yaml::parseFile($filename);

        return is_array($data) ? $data : [];
    }

    /** @param array<string, mixed> $data */
    public function dumpFile(string $filename, array $data): void
    {
        $directory = dirname($filename);
        if (!is_dir($directory) || !is_writable($directory)) {
            throw new \RuntimeException(sprintf('Le dossier « %s » n’est pas accessible en écriture.', $directory));
        }

        $temporaryFile = tempnam($directory, '.yaml-');
        if (false === $temporaryFile) {
            throw new \RuntimeException('Impossible de créer le fichier YAML temporaire.');
        }

        try {
            $yaml = Yaml::dump($data, 6, 4);
            if (false === file_put_contents($temporaryFile, $yaml, LOCK_EX) || !rename($temporaryFile, $filename)) {
                throw new \RuntimeException(sprintf('Impossible d’écrire le fichier YAML « %s ».', $filename));
            }
        } finally {
            if (is_file($temporaryFile)) {
                unlink($temporaryFile);
            }
        }
    }
}
