<?php

namespace App\Application\Reservation\Appointment\Factory;

use App\Application\Reservation\Appointment\Dto\PublicAppointmentDto;
use App\Application\Reservation\Appointment\Enum\AppointmentStatus;
use App\Application\Settings\Service\GetGeneralSettingsService;
use App\Entity\Contact;
use App\Entity\Reservation\Appointment;
use App\Entity\Reservation\Planning;

final class AppointmentFactory
{
    public function __construct(private readonly GetGeneralSettingsService $getGeneralSettingsService)
    {
    }

    public function createRequested(PublicAppointmentDto $dto, Planning $planning, Contact $contact): Appointment
    {
        $timezoneName = $this->getGeneralSettingsService->get()->timezone;
        $timezone = new \DateTimeZone($timezoneName);
        $now = new \DateTimeImmutable('now', $timezone);
        $startAt = new \DateTimeImmutable(sprintf('%s %s', $dto->date->value, $dto->time->value), $timezone);

        return (new Appointment())
            ->setPlanning($planning)
            ->setContact($contact)
            ->setCreatedAt($now)
            ->setEditedAt($now)
            ->setStartAt($startAt)
            ->setEndAt($startAt->modify(sprintf('+%d minutes', $planning->getDuration())))
            ->setTimezone((string) $dto->time->timezone)
            ->setTitle((string) $dto->contact->reason)
            ->setStatus(AppointmentStatus::REQUESTED);
    }
}
