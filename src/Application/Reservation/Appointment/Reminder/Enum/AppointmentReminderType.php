<?php

namespace App\Application\Reservation\Appointment\Reminder\Enum;

enum AppointmentReminderType: string
{
    case DAY_BEFORE = 'day_before';
    case HOUR_BEFORE = 'hour_before';

    public function reminderAt(\DateTimeImmutable $startAt): \DateTimeImmutable
    {
        return match ($this) {
            self::DAY_BEFORE => $startAt->modify('-24 hours'),
            self::HOUR_BEFORE => $startAt->modify('-1 hour'),
        };
    }
}
