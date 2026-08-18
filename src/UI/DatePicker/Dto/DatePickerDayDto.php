<?php

namespace App\UI\DatePicker\Dto;

final readonly class DatePickerDayDto
{
    public function __construct(
        public string $date,
        public string $day,
        public bool $inMonth,
        public bool $available,
        public ?string $choiceName,
    ) {
    }
}
