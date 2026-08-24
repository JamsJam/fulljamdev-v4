<?php

namespace App\UI\Calendar\Service;

use App\UI\Calendar\Builder\CalendarMonthBuilder;
use App\UI\Calendar\Builder\CalendarPeriodBuilder;
use App\UI\Calendar\Dto\CalendarDayDto;
use App\UI\Calendar\Dto\CalendarMonthDto;
use App\UI\Calendar\Dto\CalendarRangeDto;
use App\UI\Calendar\Resolver\CalendarMonthResolver;
use App\UI\Calendar\Resolver\CalendarRangeResolver;

final readonly class CalendarService
{
    public function __construct(
        private CalendarMonthResolver $monthResolver,
        private CalendarRangeResolver $rangeResolver,
        private CalendarMonthBuilder $monthBuilder,
        private CalendarPeriodBuilder $periodBuilder,
    ) {
    }

    public function resolveMonth(string|\DateTimeInterface $month): \DateTimeImmutable
    {
        return $this->monthResolver->resolve($month);
    }

    public function resolveRange(\DateTimeImmutable $month): CalendarRangeDto
    {
        return $this->rangeResolver->resolve($month);
    }

    /** @param iterable<object> $items */
    public function createMonth(\DateTimeImmutable $month, iterable $items, callable $dateResolver): CalendarMonthDto
    {
        return $this->monthBuilder->build($month, $this->resolveRange($month), $items, $dateResolver);
    }

    /**
     * @param iterable<object> $items
     *
     * @return list<CalendarDayDto>
     */
    public function createPeriod(\DateTimeImmutable $start, int $numberOfDays, iterable $items, callable $dateResolver): array
    {
        return $this->periodBuilder->build($start, $numberOfDays, $items, $dateResolver);
    }
}
