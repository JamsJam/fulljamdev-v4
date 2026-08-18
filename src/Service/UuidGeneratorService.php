<?php

namespace App\Service;

use Symfony\Component\Uid\Uuid;

final class UuidGeneratorService
{
    public function v4(): string
    {
        return Uuid::v4()->toRfc4122();
    }

    public function v7(): string
    {
        return Uuid::v7()->toRfc4122();
    }

    public function v5(string $namespace, string $name): string
    {
        return Uuid::v5(Uuid::fromString($namespace), $name)->toRfc4122();
    }
}
