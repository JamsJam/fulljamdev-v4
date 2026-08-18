<?php

namespace App\Application\Reservation\Resolver;

final class MonthResolver
{
    public function resolve(string $requestedMonth): \DateTimeImmutable
    {
        if (1 === preg_match('/^\d{4}-(?:0[1-9]|1[0-2])$/', $requestedMonth)) {
            $month = \DateTimeImmutable::createFromFormat('!Y-m', $requestedMonth);

            if (false !== $month) {
                return $month;
            }
        }

        return new \DateTimeImmutable('first day of this month 00:00:00');
    }
}
