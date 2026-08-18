<?php

namespace App\Tests\Application\Reservation\Resolver;

use App\Application\Reservation\Resolver\MonthResolver;
use PHPUnit\Framework\TestCase;

final class MonthResolverTest extends TestCase
{
    public function testItResolvesAValidMonth(): void
    {
        $month = (new MonthResolver())->resolve('2026-08');

        self::assertSame('2026-08-01 00:00:00', $month->format('Y-m-d H:i:s'));
    }

    public function testItFallsBackToTheCurrentMonthWhenTheValueIsInvalid(): void
    {
        $month = (new MonthResolver())->resolve('invalid-month');

        self::assertSame((new \DateTimeImmutable())->format('Y-m'), $month->format('Y-m'));
        self::assertSame('01 00:00:00', $month->format('d H:i:s'));
    }
}
