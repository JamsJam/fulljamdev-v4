<?php

namespace App\UI\Calendar\Builder;

use App\UI\Calendar\Dto\CalendarDayDto;

final class CalendarPeriodBuilder
{
    /**
     * @param iterable<object>                     $items
     * @param callable(object): \DateTimeInterface $dateResolver
     *
     * @return list<CalendarDayDto>
     */
    public function build(\DateTimeImmutable $start, int $numberOfDays, iterable $items, callable $dateResolver): array
    {
        $itemsByDay = [];
        foreach ($items as $item) {
            $itemsByDay[$dateResolver($item)->format('Y-m-d')][] = $item;
        }

        $days = [];
        $start = $start->setTime(0, 0);
        $todayKey = (new \DateTimeImmutable('today'))->format('Y-m-d');
        for ($offset = 0; $offset < $numberOfDays; ++$offset) {
            $date = $start->modify(sprintf('+%d days', $offset));
            $dateKey = $date->format('Y-m-d');
            $days[] = new CalendarDayDto($date, true, $dateKey === $todayKey, $itemsByDay[$dateKey] ?? []);
        }

        return $days;
    }
}
