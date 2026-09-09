<?php

namespace App\Application\Reservation\Appointment\Enum;

enum AppointmentStatus: string
{
    case REQUESTED = 'requested';
    case PROPOSED = 'proposed';
    case CONFIRMED = 'confirmed';
    case REJECTED = 'rejected';
    case OCCURRED = 'occurred';
    case COMPLETE = 'complete';
    case CANCELLED = 'cancelled';
    case NO_SHOW = 'no_show';
}
