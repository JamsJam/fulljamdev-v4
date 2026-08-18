<?php

namespace App\Tests\Application\Reservation\Appointment;

use App\Application\Reservation\Appointment\Service\SlotTimezoneConverter;
use PHPUnit\Framework\TestCase;

final class SlotTimezoneConverterTest extends TestCase
{
    public function testItConvertsAPlanningSlotToTheVisitorTimezone(): void
    {
        $converter = new SlotTimezoneConverter();

        self::assertSame(
            '04:30',
            $converter->formatTime('2026-08-19', '10:30', 'Europe/Paris', 'America/Montreal'),
        );
    }
}
