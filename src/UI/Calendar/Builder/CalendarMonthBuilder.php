<?php

namespace App\UI\Calendar\Builder;

use App\UI\Calendar\Dto\CalendarDayDto;
use App\UI\Calendar\Dto\CalendarMonthDto;
use App\UI\Calendar\Dto\CalendarRangeDto;

final class CalendarMonthBuilder
{
    /**
     * @param iterable<object>                     $items
     * @param callable(object): \DateTimeInterface $dateResolver
     */
    public function build(\DateTimeImmutable $month, CalendarRangeDto $range, iterable $items, callable $dateResolver): CalendarMonthDto
    {
        $itemsByDay = $this->groupItemsByDay($items, $dateResolver);
        $weeks = [];
        $week = [];
        $todayKey = (new \DateTimeImmutable('today'))->format('Y-m-d');

        for ($date = $range->start; $date < $range->end; $date = $date->modify('+1 day')) {
            $dateKey = $date->format('Y-m-d');
            $week[] = new CalendarDayDto(
                date: $date,
                isCurrentMonth: $date->format('Y-m') === $month->format('Y-m'),
                isToday: $dateKey === $todayKey,
                items: $itemsByDay[$dateKey] ?? [],
            );

            if (7 === count($week)) {
                $weeks[] = $week;
                $week = [];
            }
        }

        $formatter = new \IntlDateFormatter('fr_FR', pattern: 'MMMM yyyy');

        return new CalendarMonthDto(
            month: $month,
            label: ucfirst((string) $formatter->format($month)),
            previous: $month->modify('-1 month')->format('Y-m'),
            next: $month->modify('+1 month')->format('Y-m'),
            weeks: $weeks,
        );
    }

    /**
     * @param iterable<object>                     $items
     * @param callable(object): \DateTimeInterface $dateResolver
     *
     * @return array<string, list<object>>
     */
    private function groupItemsByDay(iterable $items, callable $dateResolver): array
    {
        $itemsByDay = [];
        foreach ($items as $item) {
            $itemsByDay[$dateResolver($item)->format('Y-m-d')][] = $item;
        }

        return $itemsByDay;
    }
}
