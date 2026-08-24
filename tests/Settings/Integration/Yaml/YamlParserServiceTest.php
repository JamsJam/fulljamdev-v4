<?php

namespace App\Tests\Settings\Integration\Yaml;

use App\Service\Yaml\YamlParserService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Yaml\Exception\ParseException;

final class YamlParserServiceTest extends KernelTestCase
{
    private string $yamlFile;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->yamlFile = (string) tempnam(sys_get_temp_dir(), 'yaml-parser-');
        file_put_contents($this->yamlFile, "parameters:\n    theme: light\n");
    }

    protected function tearDown(): void
    {
        if (is_file($this->yamlFile)) {
            unlink($this->yamlFile);
        }
    }

    public function testItCentralizesYamlParsingAndAtomicWriting(): void
    {
        $parser = $this->parser();
        $configuration = $parser->parseFile($this->yamlFile);
        $configuration['parameters']['timezone'] = 'America/Montreal';
        $parser->dumpFile($this->yamlFile, $configuration);

        $writtenConfiguration = $parser->parseFile($this->yamlFile);
        self::assertSame('light', $writtenConfiguration['parameters']['theme']);
        self::assertSame('America/Montreal', $writtenConfiguration['parameters']['timezone']);
    }

    public function testItReturnsAnEmptyArrayForAMissingOrEmptyFile(): void
    {
        $parser = $this->parser();

        self::assertSame([], $parser->parseFile($this->yamlFile.'.missing'));
        file_put_contents($this->yamlFile, '');
        self::assertSame([], $parser->parseFile($this->yamlFile));
    }

    public function testItRejectsInvalidYaml(): void
    {
        file_put_contents($this->yamlFile, "invalid:\n  - value\n broken");

        $this->expectException(ParseException::class);
        $this->parser()->parseFile($this->yamlFile);
    }

    public function testItRejectsWritingIntoAMissingDirectory(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('n’est pas accessible en écriture');

        $this->parser()->dumpFile(
            sys_get_temp_dir().'/missing-settings-directory/config.yaml',
            ['setting' => true],
        );
    }

    private function parser(): YamlParserService
    {
        return self::getContainer()->get(YamlParserService::class);
    }
}
