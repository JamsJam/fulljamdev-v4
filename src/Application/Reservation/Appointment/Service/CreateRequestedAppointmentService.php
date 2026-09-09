<?php

namespace App\Application\Reservation\Appointment\Service;

use App\Application\Contact\Factory\ContactFactory;
use App\Application\Contact\Persister\ContactPersister;
use App\Application\Reservation\Appointment\Dto\PublicAppointmentDto;
use App\Application\Reservation\Appointment\Factory\AppointmentFactory;
use App\Application\Reservation\Appointment\Notification\RequestedAppointmentNotifier;
use App\Application\Reservation\Appointment\Persister\AppointmentPersister;
use App\Application\Reservation\Appointment\Resolver\PublicSlotResolver;
use App\Entity\Reservation\Appointment;
use App\Entity\Reservation\Planning;
use Doctrine\ORM\EntityManagerInterface;

final readonly class CreateRequestedAppointmentService
{
    public function __construct(
        private ContactFactory $contactFactory,
        private ContactPersister $contactPersister,
        private AppointmentFactory $appointmentFactory,
        private AppointmentPersister $appointmentPersister,
        private PublicSlotResolver $slotResolver,
        private RequestedAppointmentNotifier $notifier,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function create(PublicAppointmentDto $dto, Planning $planning): Appointment
    {
        $selectedMonth = new \DateTimeImmutable(sprintf('%s-01', substr((string) $dto->date->value, 0, 7)));
        $slots = $this->slotResolver->resolveMonth($planning, $selectedMonth);
        if (!$this->slotResolver->contains($slots, (string) $dto->date->value, (string) $dto->time->value)) {
            throw new \DomainException('Ce créneau n’est plus disponible. Choisissez-en un autre.');
        }

        $contact = $this->contactFactory->createFromAppointment($dto);
        $appointment = $this->appointmentFactory->createRequested($dto, $planning, $contact);

        $this->entityManager->wrapInTransaction(function () use ($contact, $appointment): void {
            $this->contactPersister->persist($contact);
            $this->appointmentPersister->persist($appointment);
            $this->entityManager->flush();
        });

        $this->notifier->notify($appointment);

        return $appointment;
    }
}
