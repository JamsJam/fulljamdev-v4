<?php

namespace App\UI\Calendar\Resolver;

final class CalendarMonthResolver
{
    public function resolve(string|\DateTimeInterface $month): \DateTimeImmutable
    {
        if ($month instanceof \DateTimeInterface) {
            return \DateTimeImmutable::createFromInterface($month)->modify('first day of this month')->setTime(0, 0);
        }

        $resolved = \DateTimeImmutable::createFromFormat('!Y-m', $month);
        if (false === $resolved || $resolved->format('Y-m') !== $month) {
            return new \DateTimeImmutable('first day of this month midnight');
        }

        return $resolved;
    }
}
