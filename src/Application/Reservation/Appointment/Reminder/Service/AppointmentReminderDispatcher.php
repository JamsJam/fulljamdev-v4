<?php

namespace App\Application\Reservation\Appointment\Reminder\Service;

use App\Application\Reservation\Appointment\Reminder\Enum\AppointmentReminderType;
use App\Application\Reservation\Appointment\Reminder\Message\SendAppointmentReminder;
use App\Entity\Reservation\Appointment;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

final readonly class AppointmentReminderDispatcher
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private ClockInterface $clock,
    ) {
    }

    public function dispatch(Appointment $appointment): void
    {
        $appointmentId = $appointment->getId();
        $startAt = $appointment->getStartAt();

        if (null === $appointmentId || null === $startAt || null === $appointment->getLink()) {
            return;
        }

        foreach (AppointmentReminderType::cases() as $type) {
            $reminderAt = $type->reminderAt($startAt);
            $delay = $reminderAt->getTimestamp() - $this->clock->now()->getTimestamp();

            if ($delay <= 0) {
                continue;
            }

            $this->messageBus->dispatch(
                new SendAppointmentReminder(
                    $appointmentId,
                    $startAt->getTimestamp(),
                    $type,
                ),
                [new DelayStamp($delay * 1000)],
            );
        }
    }
}
