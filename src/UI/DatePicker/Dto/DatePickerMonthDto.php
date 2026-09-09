<?php

namespace App\UI\DatePicker\Dto;

final readonly class DatePickerMonthDto
{
    /** @param list<list<DatePickerDayDto>> $weeks */
    public function __construct(
        public string $label,
        public string $key,
        public string $previous,
        public string $next,
        public array $weeks,
    ) {
    }
}
