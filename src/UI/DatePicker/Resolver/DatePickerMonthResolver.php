<?php

namespace App\UI\DatePicker\Resolver;

final class DatePickerMonthResolver
{
    public function resolve(string|\DateTimeInterface $month): \DateTimeImmutable
    {
        if ($month instanceof \DateTimeInterface) {
            return \DateTimeImmutable::createFromInterface($month)
                ->modify('first day of this month')
                ->setTime(0, 0);
        }

        $resolvedMonth = \DateTimeImmutable::createFromFormat('!Y-m', $month);
        if (false === $resolvedMonth || $resolvedMonth->format('Y-m') !== $month) {
            throw new \InvalidArgumentException(sprintf('Le mois « %s » doit respecter le format YYYY-MM.', $month));
        }

        return $resolvedMonth;
    }
}
