<?php

namespace App\Tests\Application\Reservation\Appointment;

use App\Application\Reservation\Appointment\Reminder\Enum\AppointmentReminderType;
use App\Application\Reservation\Appointment\Reminder\Message\SendAppointmentReminder;
use App\Application\Reservation\Appointment\Reminder\Service\AppointmentReminderDispatcher;
use App\Entity\Reservation\Appointment;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\MockClock;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

final class AppointmentReminderDispatcherTest extends TestCase
{
    public function testItDispatchesTheDayAndHourRemindersWithTheirExactDelays(): void
    {
        $envelopes = [];
        $bus = $this->createMock(MessageBusInterface::class);
        $bus
            ->expects(self::exactly(2))
            ->method('dispatch')
            ->willReturnCallback(static function (object $message, array $stamps) use (&$envelopes): Envelope {
                return $envelopes[] = new Envelope($message, $stamps);
            });
        $dispatcher = new AppointmentReminderDispatcher(
            $bus,
            new MockClock('2026-08-18 10:00:00 Europe/Paris'),
        );
        $appointment = (new Appointment())
            ->setStartAt(new \DateTimeImmutable('2026-08-20 10:00:00 Europe/Paris'))
            ->setLink('https://meet.google.com/abc-defg-hij');
        (new \ReflectionProperty(Appointment::class, 'id'))->setValue($appointment, 42);

        $dispatcher->dispatch($appointment);

        self::assertCount(2, $envelopes);
        self::assertSame(AppointmentReminderType::DAY_BEFORE, $envelopes[0]->getMessage()->type);
        self::assertSame(AppointmentReminderType::HOUR_BEFORE, $envelopes[1]->getMessage()->type);
        self::assertSame(24 * 60 * 60 * 1000, $envelopes[0]->last(DelayStamp::class)?->getDelay());
        self::assertSame(47 * 60 * 60 * 1000, $envelopes[1]->last(DelayStamp::class)?->getDelay());
        self::assertInstanceOf(SendAppointmentReminder::class, $envelopes[0]->getMessage());
        self::assertSame(42, $envelopes[0]->getMessage()->appointmentId);
    }
}
