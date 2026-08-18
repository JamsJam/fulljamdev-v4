<?php

namespace App\Application\Reservation\Appointment\Factory;

use App\Application\Reservation\Appointment\Dto\PublicAppointmentDto;
use App\Application\Reservation\Appointment\Enum\AppointmentStatus;
use App\Entity\Contact;
use App\Entity\Reservation\Appointment;
use App\Entity\Reservation\Planning;
use App\Service\ConfigurationService;

final class AppointmentFactory
{
    public function __construct(private readonly ConfigurationService $configuration)
    {
    }

    public function createRequested(PublicAppointmentDto $dto, Planning $planning, Contact $contact): Appointment
    {
        $timezoneName = (string) $this->configuration->get('parameters.timezone', 'Europe/Paris');
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
