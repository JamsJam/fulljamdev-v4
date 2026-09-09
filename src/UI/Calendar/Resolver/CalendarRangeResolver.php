<?php

namespace App\UI\Calendar\Resolver;

use App\UI\Calendar\Dto\CalendarRangeDto;

final class CalendarRangeResolver
{
    public function resolve(\DateTimeImmutable $month): CalendarRangeDto
    {
        return new CalendarRangeDto(
            start: $month->modify('monday this week'),
            end: $month->modify('last day of this month')->modify('sunday this week')->modify('+1 day'),
        );
    }
}
