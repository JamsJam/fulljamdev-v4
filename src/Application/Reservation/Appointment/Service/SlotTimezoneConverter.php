<?php

namespace App\Application\Reservation\Appointment\Service;

final class SlotTimezoneConverter
{
    public function convert(string $date, string $time, string $sourceTimezone, string $targetTimezone): \DateTimeImmutable
    {
        return (new \DateTimeImmutable(
            sprintf('%s %s', $date, $time),
            new \DateTimeZone($sourceTimezone),
        ))->setTimezone(new \DateTimeZone($targetTimezone));
    }

    public function formatTime(string $date, string $time, string $sourceTimezone, string $targetTimezone): string
    {
        return $this->convert($date, $time, $sourceTimezone, $targetTimezone)->format('H:i');
    }
}
