<?php

namespace App\UI\Calendar\Dto;

final readonly class CalendarRangeDto
{
    public function __construct(
        public \DateTimeImmutable $start,
        public \DateTimeImmutable $end,
    ) {
    }
}
