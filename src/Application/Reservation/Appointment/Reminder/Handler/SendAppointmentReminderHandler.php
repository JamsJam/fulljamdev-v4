<?php

namespace App\Application\Reservation\Appointment\Reminder\Handler;

use App\Application\Reservation\Appointment\Enum\AppointmentStatus;
use App\Application\Reservation\Appointment\Provider\AppointmentByIdProvider;
use App\Application\Reservation\Appointment\Reminder\Message\SendAppointmentReminder;
use App\Application\Reservation\Appointment\Reminder\Notification\AppointmentReminderNotifier;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class SendAppointmentReminderHandler
{
    public function __construct(
        private AppointmentByIdProvider $appointmentProvider,
        private AppointmentReminderNotifier $notifier,
    ) {
    }

    public function __invoke(SendAppointmentReminder $message): void
    {
        $appointment = $this->appointmentProvider->provide(id: $message->appointmentId);

        if (null === $appointment
            || AppointmentStatus::CONFIRMED !== $appointment->getStatus()
            || null === $appointment->getLink()
            || $appointment->getStartAt()?->getTimestamp() !== $message->expectedStartAt) {
            return;
        }

        $this->notifier->notify($appointment, $message->type);
    }
}
