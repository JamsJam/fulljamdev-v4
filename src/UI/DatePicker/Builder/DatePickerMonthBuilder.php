<?php

namespace App\UI\DatePicker\Builder;

use App\UI\DatePicker\Dto\DatePickerDayDto;
use App\UI\DatePicker\Dto\DatePickerMonthDto;

final class DatePickerMonthBuilder
{
    /** @param list<string> $availableDates */
    public function build(\DateTimeImmutable $month, array $availableDates): DatePickerMonthDto
    {
        $month = $month->modify('first day of this month')->setTime(0, 0);
        $gridStart = $month->modify(sprintf('-%d days', (int) $month->format('N') - 1));
        $weeks = [];

        for ($week = 0; $week < 6; ++$week) {
            $days = [];
            for ($day = 0; $day < 7; ++$day) {
                $date = $gridStart->modify(sprintf('+%d days', $week * 7 + $day));
                $dateKey = $date->format('Y-m-d');
                $inMonth = $date->format('Y-m') === $month->format('Y-m');
                $available = $inMonth && in_array($dateKey, $availableDates, true);
                $days[] = new DatePickerDayDto(
                    date: $dateKey,
                    day: $date->format('d'),
                    inMonth: $inMonth,
                    available: $available,
                    choiceName: $available ? str_replace('-', '_', $dateKey) : null,
                );
            }
            $weeks[] = $days;
        }

        $formatter = new \IntlDateFormatter('fr_FR', pattern: 'MMMM yyyy');

        return new DatePickerMonthDto(
            label: mb_strtoupper((string) $formatter->format($month)),
            key: $month->format('Y-m'),
            previous: $month->modify('-1 month')->format('Y-m'),
            next: $month->modify('+1 month')->format('Y-m'),
            weeks: $weeks,
        );
    }
}
