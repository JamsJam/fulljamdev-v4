<?php

namespace App\Application\Reservation\Appointment\Reminder\Message;

use App\Application\Reservation\Appointment\Reminder\Enum\AppointmentReminderType;

final readonly class SendAppointmentReminder
{
    public function __construct(
        public int $appointmentId,
        public int $expectedStartAt,
        public AppointmentReminderType $type,
    ) {
    }
}
