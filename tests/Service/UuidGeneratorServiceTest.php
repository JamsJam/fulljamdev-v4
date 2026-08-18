<?php

namespace App\Tests\Service;

use App\Service\UuidGeneratorService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class UuidGeneratorServiceTest extends TestCase
{
    public function testItGeneratesSupportedUuidVersions(): void
    {
        $generator = new UuidGeneratorService();

        self::assertTrue(Uuid::isValid($generator->v4()));
        self::assertTrue(Uuid::isValid($generator->v7()));
        self::assertSame(
            $generator->v5(Uuid::NAMESPACE_DNS, 'example.com'),
            $generator->v5(Uuid::NAMESPACE_DNS, 'example.com'),
        );
    }
}
