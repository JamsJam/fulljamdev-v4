<?php

namespace App\Tests\Reservation\Unit\Appointment;

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

    public function testItHandlesADaylightSavingTimeBoundary(): void
    {
        self::assertSame(
            '01:30',
            (new SlotTimezoneConverter())->formatTime('2026-03-29', '03:30', 'Europe/Paris', 'UTC'),
        );
    }

    public function testItRejectsAnUnknownTimezone(): void
    {
        $this->expectException(\DateInvalidTimeZoneException::class);

        (new SlotTimezoneConverter())->convert('2026-08-19', '10:30', 'Unknown/Timezone', 'UTC');
    }
}
