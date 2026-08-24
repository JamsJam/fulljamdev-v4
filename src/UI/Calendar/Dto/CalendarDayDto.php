<?php

namespace App\UI\Calendar\Dto;

final readonly class CalendarDayDto
{
    /** @param list<object> $items */
    public function __construct(
        public \DateTimeImmutable $date,
        public bool $isCurrentMonth,
        public bool $isToday,
        public array $items,
    ) {
    }
}
