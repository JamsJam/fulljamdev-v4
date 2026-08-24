<?php

namespace App\UI\Calendar\Dto;

final readonly class CalendarMonthDto
{
    /** @param list<list<CalendarDayDto>> $weeks */
    public function __construct(
        public \DateTimeImmutable $month,
        public string $label,
        public string $previous,
        public string $next,
        public array $weeks,
    ) {
    }
}
