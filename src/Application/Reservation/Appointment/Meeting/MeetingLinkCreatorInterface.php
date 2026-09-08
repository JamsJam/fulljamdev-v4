<?php

namespace App\Application\Reservation\Appointment\Meeting;

use App\Entity\Reservation\Appointment;

interface MeetingLinkCreatorInterface
{
    public function create(Appointment $appointment): string;
}
