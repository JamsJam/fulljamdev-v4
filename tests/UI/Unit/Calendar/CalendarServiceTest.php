<?php

namespace App\Tests\UI\Unit\Calendar;

use App\UI\Calendar\Builder\CalendarMonthBuilder;
use App\UI\Calendar\Builder\CalendarPeriodBuilder;
use App\UI\Calendar\Resolver\CalendarMonthResolver;
use App\UI\Calendar\Resolver\CalendarRangeResolver;
use App\UI\Calendar\Service\CalendarService;
use PHPUnit\Framework\TestCase;

final class CalendarServiceTest extends TestCase
{
    public function testItBuildsAMonthCalendarAndGroupsItemsByDay(): void
    {
        $service = $this->createService();
        $item = new CalendarItem(new \DateTimeImmutable('2026-08-20 10:00:00'));
        $month = $service->resolveMonth('2026-08');
        $calendar = $service->createMonth($month, [$item], static fn (CalendarItem $item): \DateTimeImmutable => $item->date);

        self::assertSame('2026-08', $calendar->month->format('Y-m'));
        self::assertSame('2026-07', $calendar->previous);
        self::assertSame('2026-09', $calendar->next);
        self::assertCount(6, $calendar->weeks);
        self::assertSame([$item], $calendar->weeks[3][3]->items);
    }

    public function testItBuildsAThreeDayPeriod(): void
    {
        $service = $this->createService();
        $item = new CalendarItem(new \DateTimeImmutable('2026-08-21 14:00:00'));
        $days = $service->createPeriod(
            new \DateTimeImmutable('2026-08-20 09:30:00'),
            3,
            [$item],
            static fn (CalendarItem $item): \DateTimeImmutable => $item->date,
        );

        self::assertCount(3, $days);
        self::assertSame('2026-08-20', $days[0]->date->format('Y-m-d'));
        self::assertSame([$item], $days[1]->items);
    }

    public function testItFallsBackToTheCurrentMonthForAnInvalidMonth(): void
    {
        self::assertSame(
            (new \DateTimeImmutable('first day of this month'))->format('Y-m'),
            $this->createService()->resolveMonth('not-a-month')->format('Y-m'),
        );
    }

    public function testItBuildsAnEmptyPeriodWhenTheNumberOfDaysIsZero(): void
    {
        self::assertSame([], $this->createService()->createPeriod(
            new \DateTimeImmutable('2026-08-20'),
            0,
            [],
            static fn (CalendarItem $item): \DateTimeImmutable => $item->date,
        ));
    }

    private function createService(): CalendarService
    {
        return new CalendarService(
            new CalendarMonthResolver(),
            new CalendarRangeResolver(),
            new CalendarMonthBuilder(),
            new CalendarPeriodBuilder(),
        );
    }
}

final readonly class CalendarItem
{
    public function __construct(public \DateTimeImmutable $date)
    {
    }
}
