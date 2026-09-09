<?php

namespace App\Tests\Reservation\Unit\Mapper;

use App\Application\Reservation\Availability\Mapper\DayMapper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class DayMapperTest extends TestCase
{
    /** @return iterable<string, array{int, string}> */
    public static function days(): iterable
    {
        yield 'lundi' => [1, 'lundi'];
        yield 'mardi' => [2, 'mardi'];
        yield 'mercredi' => [3, 'mercredi'];
        yield 'jeudi' => [4, 'jeudi'];
        yield 'vendredi' => [5, 'vendredi'];
        yield 'samedi' => [6, 'samedi'];
        yield 'dimanche' => [7, 'dimanche'];
    }

    #[DataProvider('days')]
    public function testItMapsNumberToDay(int $number, string $day): void
    {
        self::assertSame($day, (new DayMapper())->numberToDay($number));
    }

    #[DataProvider('days')]
    public function testItMapsDayToNumber(int $number, string $day): void
    {
        self::assertSame($number, (new DayMapper())->dayToNumber($day));
    }

    public function testDayMappingIsCaseInsensitiveAndTrimmed(): void
    {
        self::assertSame(1, (new DayMapper())->dayToNumber(' LUNDI '));
    }

    public function testItRejectsAnInvalidNumber(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new DayMapper())->numberToDay(8);
    }

    public function testItRejectsAnInvalidDay(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new DayMapper())->dayToNumber('inconnu');
    }
}
