<?php

namespace App\Tests\UI\DatePicker;

use App\UI\DatePicker\Builder\DatePickerMonthBuilder;
use PHPUnit\Framework\TestCase;

final class DatePickerMonthBuilderTest extends TestCase
{
    public function testItBuildsOnlyTheRequestedMonthWithItsNavigation(): void
    {
        $calendar = (new DatePickerMonthBuilder())->build(
            new \DateTimeImmutable('2026-08-01'),
            ['2026-08-19', '2026-09-01'],
        );

        self::assertSame('2026-08', $calendar->key);
        self::assertSame('2026-07', $calendar->previous);
        self::assertSame('2026-09', $calendar->next);
        self::assertCount(6, $calendar->weeks);

        $days = array_merge(...$calendar->weeks);
        $availableDays = array_values(array_filter($days, static fn ($day): bool => $day->available));

        self::assertCount(1, $availableDays);
        self::assertSame('2026-08-19', $availableDays[0]->date);
    }
}
